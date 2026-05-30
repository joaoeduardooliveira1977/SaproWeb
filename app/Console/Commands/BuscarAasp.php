<?php

namespace App\Console\Commands;

use App\Models\{AaspAdvogado, AaspConfig, AaspPublicacao, Processo, Tenant};
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BuscarAasp extends Command
{
    protected $signature   = 'aasp:buscar {--data= : Data no formato Y-m-d (padrão: hoje)}';
    protected $description = 'Busca publicações AASP do dia para todos os advogados ativos de cada tenant';

    public function handle(): int
    {
        $data          = $this->option('data') ? Carbon::parse($this->option('data')) : today();
        $dataFormatada = $data->format('d/m/Y');

        $this->info("AASP — Buscando publicações de {$dataFormatada}");

        $totalGeral = 0;

        // Itera por tenant ativo — sem Global Scope ativo no console,
        // precisamos filtrar explicitamente por tenant_id
        Tenant::where('ativo', true)->each(function (Tenant $tenant) use ($data, $dataFormatada, &$totalGeral) {

            // Verifica se a busca está habilitada para este tenant
            $config = AaspConfig::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenant->id)
                ->first();

            if ($config && !$config->ativo) {
                return; // continua para o próximo tenant
            }

            $advogados = AaspAdvogado::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenant->id)
                ->where('ativo', true)
                ->get();

            if ($advogados->isEmpty()) {
                return;
            }

            $this->line("  <comment>Tenant:</comment> {$tenant->nome}");

            foreach ($advogados as $adv) {
                try {
                    $response = Http::timeout(30)
                        ->get('https://intimacaoapi.aasp.org.br/api/Associado/intimacao/json', [
                            'chave'       => $adv->chave_aasp,
                            'data'        => $dataFormatada,
                            'diferencial' => 'false',
                        ]);

                    if (!$response->successful()) {
                        $this->warn("    {$adv->nome}: HTTP {$response->status()}");
                        continue;
                    }

                    $payload = $response->json();
                    $pubs    = $payload['intimacoes'] ?? $payload['value'] ?? (isset($payload[0]) ? $payload : []);

                    $count = 0;
                    foreach ($pubs as $pub) {
                        $numPub = $pub['numeroPublicacao'] ?? $pub['numero_publicacao'] ?? $pub['NumeroPublicacao'] ?? null;

                        // Evita duplicata por tenant + numero_publicacao + advogado
                        if ($numPub && AaspPublicacao::withoutGlobalScope('tenant')
                            ->where('tenant_id', $tenant->id)
                            ->where('numero_publicacao', $numPub)
                            ->where('codigo_aasp', $adv->codigo_aasp)
                            ->exists()
                        ) {
                            continue;
                        }

                        try {
                            $dataPub = Carbon::parse($pub['data'] ?? $pub['dataPublicacao'] ?? $data)->format('Y-m-d');
                        } catch (\Exception) {
                            $dataPub = $data->format('Y-m-d');
                        }

                        $jornalRaw = $pub['jornal'] ?? null;
                        $jornal    = is_array($jornalRaw) ? ($jornalRaw['nomeJornal'] ?? '') : ($jornalRaw ?? '');

                        $numProcesso = $pub['numeroUnicoProcesso'] ?? $pub['numeroProcesso'] ?? $pub['numero_processo'] ?? '';
                        $processoId  = $numProcesso
                            ? Processo::withoutGlobalScope('tenant')
                                ->where('tenant_id', $tenant->id)
                                ->where('numero', $numProcesso)
                                ->value('id')
                            : null;

                        AaspPublicacao::withoutGlobalScope('tenant')->create([
                            'tenant_id'         => $tenant->id,
                            'codigo_aasp'       => $adv->codigo_aasp,
                            'processo_id'       => $processoId,
                            'data'              => $dataPub,
                            'jornal'            => $jornal,
                            'numero_processo'   => $numProcesso,
                            'titulo'            => $pub['titulo'] ?? $pub['Titulo'] ?? '',
                            'texto'             => $pub['textoPublicacao'] ?? $pub['texto'] ?? $pub['conteudo'] ?? '',
                            'numero_publicacao' => $numPub,
                        ]);
                        $count++;
                        $totalGeral++;
                    }

                    $this->line("    <info>{$adv->nome}</info>: {$count} nova(s)");

                } catch (\Throwable $e) {
                    $this->warn("    {$adv->nome}: {$e->getMessage()}");
                }
            }
        });

        $this->info("Concluído — {$totalGeral} publicação(ões) importada(s).");
        return self::SUCCESS;
    }
}
