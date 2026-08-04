<?php

namespace App\Console\Commands;

use App\Models\Pessoa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportarSenhasPortalBittar extends Command
{
    /**
     * Casos especiais conhecidos do CSV (ver storage/app/migracao_arruda/credenciais_portal_bittar.csv):
     * CNPJ duplicado entre dois clientes (7/938 e 139/68) e CNPJ inválido (256, 219, 216).
     * Casam normalmente por cod_legado; ficam impedidos de logar por CNPJ até ajuste manual do cadastro.
     */
    private const COD_LEGADO_CASOS_ESPECIAIS = ['7', '938', '139', '68', '256', '219', '216'];

    protected $signature = 'portal:importar-senhas-bittar
        {--tenant=5 : ID do tenant}
        {--slug=bittar-arruda : slug esperado do tenant (checagem de segurança contra banco errado)}
        {--commit : Efetiva a gravação. Sem esta flag, roda em modo dry-run (ROLLBACK ao final)}';

    protected $description = 'Importa senhas do portal do cliente (Arruda & Bittar) do CSV legado, casando pessoas por id_legado';

    public function handle(): int
    {
        $tenantId     = (int) $this->option('tenant');
        $slugEsperado = (string) $this->option('slug');
        $commit       = (bool) $this->option('commit');

        $csvPath = storage_path('app/migracao_arruda/credenciais_portal_bittar.csv');
        if (! is_file($csvPath)) {
            $this->error("CSV não encontrado: {$csvPath}");
            return self::FAILURE;
        }

        $tenant = DB::table('tenants')->where('id', $tenantId)->first();
        if (! $tenant) {
            $this->error("Tenant {$tenantId} não existe neste banco. Abortando — provável banco errado.");
            return self::FAILURE;
        }
        if ($tenant->slug !== $slugEsperado) {
            $this->error("Tenant {$tenantId} tem slug '{$tenant->slug}', esperado '{$slugEsperado}'. Abortando.");
            return self::FAILURE;
        }

        $linhas  = array_map('str_getcsv', file($csvPath));
        $cabecalho = array_map('trim', array_shift($linhas));

        $totalCsv         = 0;
        $naoEncontrados    = [];
        $divergenciasCnpj  = [];
        $atualizadosIds    = [];
        $resultadoPorCod   = [];

        DB::beginTransaction();

        try {
            foreach ($linhas as $linha) {
                if (count($linha) < count($cabecalho) || trim((string) ($linha[0] ?? '')) === '') {
                    continue;
                }

                $dado        = array_combine($cabecalho, $linha);
                $codLegado   = trim($dado['cod_legado']);
                $cnpjCsv     = preg_replace('/\D/', '', $dado['cnpj']);
                $senha       = $dado['senha'];
                $razaoSocial = trim($dado['razao_social']);
                $totalCsv++;

                $pessoa = Pessoa::where('tenant_id', $tenantId)
                    ->where('id_legado', $codLegado)
                    ->where('fonte_legado', 'empresa')
                    ->first();

                if (! $pessoa) {
                    $naoEncontrados[] = "{$codLegado} - {$razaoSocial}";
                    $resultadoPorCod[$codLegado] = "não encontrado (cod_legado sem pessoa em tenant {$tenantId})";
                    continue;
                }

                $cnpjPessoa = preg_replace('/\D/', '', (string) $pessoa->cpf_cnpj);
                $divergiu   = $cnpjCsv !== $cnpjPessoa;
                if ($divergiu) {
                    $divergenciasCnpj[] = sprintf(
                        'cod_legado=%s pessoa_id=%s csv_cnpj=%s pessoa_cnpj=%s (%s)',
                        $codLegado,
                        $pessoa->id,
                        $cnpjCsv,
                        $cnpjPessoa,
                        $razaoSocial
                    );
                }

                $pessoa->portal_senha            = Hash::make($senha);
                $pessoa->portal_ativo             = true;
                $pessoa->portal_senha_provisoria = true;
                $pessoa->save();

                $atualizadosIds[] = $pessoa->id;
                $resultadoPorCod[$codLegado] = sprintf(
                    'atualizado (pessoa_id=%s, cnpj_csv=%s, cnpj_pessoa=%s%s)',
                    $pessoa->id,
                    $cnpjCsv,
                    $cnpjPessoa,
                    $divergiu ? ', DIVERGE' : ', confere'
                );
            }

            // ── Verificação antes do COMMIT ──────────────────────────
            $countAtivos = Pessoa::where('tenant_id', $tenantId)->where('portal_ativo', true)->count();

            $countHashInvalido = $atualizadosIds === [] ? 0 : Pessoa::whereIn('id', $atualizadosIds)
                ->where(function ($q) {
                    $q->whereNull('portal_senha')->orWhere('portal_senha', 'not like', '$2y$%');
                })
                ->count();

            $countProvisoriaFaltando = $atualizadosIds === [] ? 0 : Pessoa::whereIn('id', $atualizadosIds)
                ->where('portal_senha_provisoria', false)
                ->count();

            $outrosTenantsTocados = $atualizadosIds === [] ? 0 : DB::table('pessoas')
                ->whereIn('id', $atualizadosIds)
                ->where('tenant_id', '!=', $tenantId)
                ->count();

            $this->info('== Resultado ==');
            $this->info("Linhas no CSV: {$totalCsv}");
            $this->info('Atualizadas: ' . count($atualizadosIds));
            $this->info('Não casadas por cod_legado: ' . count($naoEncontrados));
            $this->info('Divergências de CNPJ: ' . count($divergenciasCnpj));
            $this->info("Pessoas do tenant {$tenantId} com portal_ativo=true agora: {$countAtivos}");

            if ($naoEncontrados !== []) {
                $this->warn('-- Não encontrados (cod_legado - razão social) --');
                foreach ($naoEncontrados as $item) {
                    $this->line("  {$item}");
                }
            }

            if ($divergenciasCnpj !== []) {
                $this->warn('-- Divergências de CNPJ (importado mesmo assim) --');
                foreach ($divergenciasCnpj as $item) {
                    $this->line("  {$item}");
                }
            }

            $this->warn('-- Casos especiais conhecidos (revisão manual — login por CNPJ pode ficar inviável) --');
            foreach (self::COD_LEGADO_CASOS_ESPECIAIS as $cod) {
                $status = $resultadoPorCod[$cod] ?? 'não presente no CSV processado';
                $this->line("  cod_legado={$cod}: {$status}");
            }

            if ($countHashInvalido > 0) {
                throw new \RuntimeException("{$countHashInvalido} senha(s) não hasheada(s) corretamente (esperado prefixo \$2y\$). Abortando.");
            }
            if ($countProvisoriaFaltando > 0) {
                throw new \RuntimeException("{$countProvisoriaFaltando} pessoa(s) atualizada(s) sem portal_senha_provisoria=true. Abortando.");
            }
            if ($outrosTenantsTocados > 0) {
                throw new \RuntimeException("ALERTA: {$outrosTenantsTocados} registro(s) de OUTRO tenant foram tocados. Abortando.");
            }

            if (! $commit) {
                $this->warn('Modo dry-run (sem --commit). Nenhuma alteração persistida — ROLLBACK.');
                DB::rollBack();
                return self::SUCCESS;
            }

            DB::commit();
            $this->info('COMMIT realizado com sucesso.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Erro — ROLLBACK realizado: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
