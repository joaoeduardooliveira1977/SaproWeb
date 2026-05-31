<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\OnboardingService;

class PopularDadosFicticios implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1; // Não tentar novamente em caso de falha

    public function __construct(
        private readonly int $tenantId,
        private readonly int $adminUserId
    ) {}

    public function handle(): void
    {
        $tid     = $this->tenantId;
        $adminId = $this->adminUserId;

        Log::info("PopularDados [{$tid}]: iniciando para tenant={$tid}, admin={$adminId}");

        try {
            // ── Guarda de idempotência ─────────────────────────────────
            $pessoasExistentes = DB::table('pessoas')->where('tenant_id', $tid)->count();
            Log::info("PopularDados [{$tid}]: pessoas existentes = {$pessoasExistentes}");

            if ($pessoasExistentes > 0) {
                Log::info("PopularDados [{$tid}]: tenant já tem dados, pulando.");
                return;
            }

            // ── Lookups — buscar ou criar para este tenant ─────────────
            $faseId      = $this->garantirFase($tid);
            $riscoId     = $this->garantirRisco($tid);
            $tipoTrabId  = $this->garantirTipoAcao($tid, 'Trabalhista', 'TRB');
            $tipoCivelId = $this->garantirTipoAcao($tid, 'Cível', 'CVL');

            Log::info("PopularDados [{$tid}]: fase={$faseId}, risco={$riscoId}, trabalhista={$tipoTrabId}, civel={$tipoCivelId}");

            $now = now();

            // ── Pessoas — identificadas por nome+tenant (CPF varia por tenant) ──
            $p1 = $this->upsertPessoa($tid, 'João Carlos Silva',         'fisica',   $now, 'Cliente');
            $p2 = $this->upsertPessoa($tid, 'Maria Fernanda Costa',      'fisica',   $now, 'Cliente');
            $p3 = $this->upsertPessoa($tid, 'Construtora Horizonte Ltda','juridica', $now, 'Cliente');
            $p4 = $this->upsertPessoa($tid, 'Pedro Almeida Santos',      'fisica',   $now, 'Parte Contrária');

            Log::info("PopularDados [{$tid}]: pessoas criadas: p1={$p1}, p2={$p2}, p3={$p3}, p4={$p4}");

            // ── Processos ──────────────────────────────────────────────
            $proc1 = $this->upsertProcesso($tid, "0001234-55.2024.5.02.{$tid}", [
                'cliente_id'         => $p1,
                'parte_contraria'    => 'Construtora Horizonte Ltda',
                'parte_contraria_id' => $p3,
                'tipo_acao_id'       => $tipoTrabId,
                'fase_id'            => $faseId,
                'risco_id'           => $riscoId,
                'extrajudicial'      => false,
                'autor_reu'          => 'Autor',
                'valor_causa'        => 45000.00,
                'status'             => 'Ativo',
                'criado_por'         => $adminId,
            ], $now);

            $proc2 = $this->upsertProcesso($tid, "0009876-11.2024.8.26.{$tid}", [
                'cliente_id'         => $p2,
                'parte_contraria'    => 'Pedro Almeida Santos',
                'parte_contraria_id' => $p4,
                'tipo_acao_id'       => $tipoCivelId,
                'fase_id'            => $faseId,
                'risco_id'           => $riscoId,
                'extrajudicial'      => false,
                'autor_reu'          => 'Autor',
                'valor_causa'        => 15000.00,
                'status'             => 'Ativo',
                'criado_por'         => $adminId,
            ], $now);

            $proc3 = $this->upsertProcesso($tid, "EXT-{$tid}-001", [
                'cliente_id'    => $p3,
                'extrajudicial' => true,
                'autor_reu'     => 'Requerente',
                'valor_causa'   => 8000.00,
                'status'        => 'Ativo',
                'criado_por'    => $adminId,
            ], $now);

            Log::info("PopularDados [{$tid}]: processos criados: proc1={$proc1}, proc2={$proc2}, proc3={$proc3}");

            // ── Prazos ─────────────────────────────────────────────────
            $prazosConfig = [
                ['titulo' => 'Apresentar contestação', 'tipo' => 'Prazo Fatal', 'processo_id' => $proc1, 'dias' => 7,  'prazo_fatal' => true],
                ['titulo' => 'Audiência de instrução',  'tipo' => 'Audiência',   'processo_id' => $proc2, 'dias' => 15, 'prazo_fatal' => false],
                ['titulo' => 'Recurso de apelação',     'tipo' => 'Prazo',       'processo_id' => $proc1, 'dias' => 30, 'prazo_fatal' => false],
            ];

            foreach ($prazosConfig as $p) {
                $this->upsertPrazo($tid, $p, $adminId, $now);
            }

            // ── Andamentos ─────────────────────────────────────────────
            $this->upsertAndamento($tid, $proc1, now()->subDays(30)->toDateString(),
                'Petição inicial protocolada. Aguardando designação de audiência.', $adminId, $now);

            $this->upsertAndamento($tid, $proc2, now()->subDays(15)->toDateString(),
                'Citação realizada. Prazo de contestação iniciado.', $adminId, $now);

            Log::info("PopularDados [{$tid}]: concluído com sucesso.");

            OnboardingService::inicializarParaTenant($tid);

        } catch (\Throwable $e) {
            Log::error("PopularDados [{$tid}]: ERRO — " . $e->getMessage(), [
                'tenant_id' => $tid,
                'file'      => $e->getFile() . ':' . $e->getLine(),
                'trace'     => substr($e->getTraceAsString(), 0, 1000),
            ]);
            throw $e; // Re-lança para o queue marcar como failed
        }
    }

    // ── Lookups — buscar OU criar de forma segura ─────────────────────

    private function garantirFase(int $tid): ?int
    {
        // 1. Fase própria do tenant
        $id = DB::table('fases')->where('tenant_id', $tid)->orderBy('ordem')->value('id');
        if ($id) {
            Log::info("PopularDados [{$tid}]: fase encontrada no tenant: {$id}");
            return $id;
        }

        // 2. Fase global (sem tenant)
        $id = DB::table('fases')->whereNull('tenant_id')->orderBy('ordem')->value('id');
        if ($id) {
            Log::info("PopularDados [{$tid}]: fase global encontrada: {$id}");
            return $id;
        }

        // 3. Qualquer fase disponível no banco
        $id = DB::table('fases')->orderBy('id')->value('id');
        if ($id) {
            Log::info("PopularDados [{$tid}]: fase de outro tenant reutilizada: {$id}");
            return $id;
        }

        // 4. Banco sem nenhuma fase — criar com código único por tenant
        $codigo = 'F' . str_pad((string) $tid, 5, '0', STR_PAD_LEFT); // F00008, max 7 chars
        $id = DB::table('fases')->insertGetId([
            'tenant_id'  => $tid,
            'descricao'  => 'Instrução',
            'codigo'     => $codigo,
            'ordem'      => 1,
            'ativo'      => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Log::info("PopularDados [{$tid}]: fase criada com codigo={$codigo}, id={$id}");
        return $id;
    }

    private function garantirRisco(int $tid): ?int
    {
        $id = DB::table('graus_risco')->where('tenant_id', $tid)->orderBy('id')->value('id');
        if ($id) return $id;

        $id = DB::table('graus_risco')->whereNull('tenant_id')->orderBy('id')->value('id');
        if ($id) return $id;

        $id = DB::table('graus_risco')->orderBy('id')->value('id');
        if ($id) return $id;

        // Criar — codigo unique foi removido da tabela graus_risco
        $id = DB::table('graus_risco')->insertGetId([
            'tenant_id'  => $tid,
            'descricao'  => 'Médio',
            'codigo'     => 'M' . $tid,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Log::info("PopularDados [{$tid}]: risco criado id={$id}");
        return $id;
    }

    private function garantirTipoAcao(int $tid, string $descricao, string $codigoBase): ?int
    {
        // Tenta match por descricao (tenant próprio → global → qualquer)
        foreach ([
            fn($q) => $q->where('tenant_id', $tid)->where('descricao', $descricao),
            fn($q) => $q->whereNull('tenant_id')->where('descricao', $descricao),
            fn($q) => $q->where('descricao', $descricao),
            fn($q) => $q->where('tenant_id', $tid),
            fn($q) => $q->whereNull('tenant_id'),
            fn($q) => $q,
        ] as $scope) {
            $id = DB::table('tipos_acao')->tap($scope)->orderBy('id')->value('id');
            if ($id) return $id;
        }

        // Criar — codigo unique foi removido da tabela tipos_acao
        $id = DB::table('tipos_acao')->insertGetId([
            'tenant_id'  => $tid,
            'descricao'  => $descricao,
            'codigo'     => $codigoBase . $tid,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Log::info("PopularDados [{$tid}]: tipo_acao '{$descricao}' criado id={$id}");
        return $id;
    }

    // ── Dados fictícios — idempotentes ────────────────────────────────

    private function upsertPessoa(int $tid, string $nome, string $tipo_pessoa, $now, string $tipo): int
    {
        $existing = DB::table('pessoas')
            ->where('tenant_id', $tid)
            ->where('nome', $nome)
            ->value('id');

        if ($existing) {
            Log::info("PopularDados [{$tid}]: pessoa '{$nome}' já existe (id={$existing})");
            return $existing;
        }

        $id = DB::table('pessoas')->insertGetId([
            'tenant_id'   => $tid,
            'nome'        => $nome,
            'tipo_pessoa' => $tipo_pessoa,
            'ativo'       => true,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        // Tipo da pessoa
        if (!DB::table('pessoa_tipos')->where('pessoa_id', $id)->where('tipo', $tipo)->exists()) {
            DB::table('pessoa_tipos')->insert(['pessoa_id' => $id, 'tipo' => $tipo]);
        }

        return $id;
    }

    private function upsertProcesso(int $tid, string $numero, array $dados, $now): int
    {
        $existing = DB::table('processos')
            ->where('tenant_id', $tid)
            ->where('numero', $numero)
            ->value('id');

        if ($existing) {
            Log::info("PopularDados [{$tid}]: processo '{$numero}' já existe (id={$existing})");
            return $existing;
        }

        return DB::table('processos')->insertGetId([
            'tenant_id'  => $tid,
            'numero'     => $numero,
            'created_at' => $now,
            'updated_at' => $now,
            ...$dados,
        ]);
    }

    private function upsertPrazo(int $tid, array $p, int $adminId, $now): void
    {
        if (DB::table('prazos')
            ->where('tenant_id', $tid)
            ->where('processo_id', $p['processo_id'])
            ->where('titulo', $p['titulo'])
            ->exists()
        ) return;

        DB::table('prazos')->insert([
            'tenant_id'     => $tid,
            'processo_id'   => $p['processo_id'],
            'titulo'        => $p['titulo'],
            'tipo'          => $p['tipo'],
            'data_inicio'   => now()->toDateString(),
            'tipo_contagem' => 'corridos',
            'dias'          => $p['dias'],
            'data_prazo'    => now()->addDays($p['dias'])->toDateString(),
            'prazo_fatal'   => $p['prazo_fatal'],
            'status'        => 'aberto',
            'criado_por'    => $adminId,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
    }

    private function upsertAndamento(int $tid, int $processoId, string $data, string $descricao, int $adminId, $now): void
    {
        if (DB::table('andamentos')
            ->where('tenant_id', $tid)
            ->where('processo_id', $processoId)
            ->where('descricao', $descricao)
            ->exists()
        ) return;

        DB::table('andamentos')->insert([
            'tenant_id'   => $tid,
            'processo_id' => $processoId,
            'data'        => $data,
            'descricao'   => $descricao,
            'interno'     => false,
            'usuario_id'  => $adminId,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);
    }
}
