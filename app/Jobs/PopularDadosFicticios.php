<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Services\OnboardingService;

class PopularDadosFicticios implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $tenantId,
        private readonly int $adminUserId
    ) {}

    public function handle(): void
    {
        $tid     = $this->tenantId;
        $adminId = $this->adminUserId;

        // Idempotente: não popular de novo se o tenant já tem pessoas
        if (DB::table('pessoas')->where('tenant_id', $tid)->count() > 0) return;

        // ── Lookup de tabelas compartilhadas (somente leitura) ────────
        // Busca: 1) registros do próprio tenant  2) registros globais (null)  3) qualquer registro
        $faseId      = $this->buscarFase($tid);
        $riscoId     = $this->buscarRisco($tid);
        $tipoTrabId  = $this->buscarTipoAcao($tid, 'Trabalhista');
        $tipoCivelId = $this->buscarTipoAcao($tid, 'Cível');

        $now = now();

        // ── Pessoas (firstOrCreate por tenant_id + cpf_cnpj) ─────────
        $p1 = $this->upsertPessoa($tid, [
            'nome'        => 'João Carlos Silva',
            'tipo_pessoa' => 'fisica',
            'cpf_cnpj'    => '111.222.333-96',
            'telefone'    => '(11) 91234-5678',
            'cidade'      => 'São Paulo',
            'estado'      => 'SP',
        ], 'Cliente', $now);

        $p2 = $this->upsertPessoa($tid, [
            'nome'        => 'Maria Fernanda Costa',
            'tipo_pessoa' => 'fisica',
            'cpf_cnpj'    => '222.333.444-07',
            'telefone'    => '(11) 92345-6789',
            'cidade'      => 'Campinas',
            'estado'      => 'SP',
        ], 'Cliente', $now);

        $p3 = $this->upsertPessoa($tid, [
            'nome'        => 'Construtora Horizonte Ltda',
            'tipo_pessoa' => 'juridica',
            'cpf_cnpj'    => '11.222.333/0001-81',
            'telefone'    => '(11) 3456-7890',
            'cidade'      => 'São Paulo',
            'estado'      => 'SP',
        ], 'Cliente', $now);

        $p4 = $this->upsertPessoa($tid, [
            'nome'        => 'Pedro Almeida Santos',
            'tipo_pessoa' => 'fisica',
            'cpf_cnpj'    => '333.444.555-18',
            'telefone'    => '(11) 93456-7890',
            'cidade'      => 'Guarulhos',
            'estado'      => 'SP',
        ], 'Parte Contrária', $now);

        // ── Processos (upsert por tenant_id + numero) ─────────────────
        $proc1 = $this->upsertProcesso($tid, '0001234-55.2024.5.02.0001', [
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

        $proc2 = $this->upsertProcesso($tid, '0009876-11.2024.8.26.0100', [
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

        $proc3 = $this->upsertProcesso($tid, 'EXT-2024-001', [
            'cliente_id'    => $p3,
            'extrajudicial' => true,
            'autor_reu'     => 'Requerente',
            'valor_causa'   => 8000.00,
            'status'        => 'Ativo',
            'criado_por'    => $adminId,
        ], $now);

        // ── Prazos ────────────────────────────────────────────────────
        $prazosConfig = [
            ['titulo' => 'Apresentar contestação',  'tipo' => 'Prazo Fatal', 'processo_id' => $proc1, 'dias' => 7,  'prazo_fatal' => true],
            ['titulo' => 'Audiência de instrução',  'tipo' => 'Audiência',   'processo_id' => $proc2, 'dias' => 15, 'prazo_fatal' => false],
            ['titulo' => 'Recurso de apelação',     'tipo' => 'Prazo',       'processo_id' => $proc1, 'dias' => 30, 'prazo_fatal' => false],
        ];

        foreach ($prazosConfig as $p) {
            $jaExiste = DB::table('prazos')
                ->where('tenant_id', $tid)
                ->where('processo_id', $p['processo_id'])
                ->where('titulo', $p['titulo'])
                ->exists();

            if (!$jaExiste) {
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
        }

        // ── Andamentos ────────────────────────────────────────────────
        $andamentos = [
            ['processo_id' => $proc1, 'data' => now()->subDays(30)->toDateString(), 'descricao' => 'Petição inicial protocolada. Aguardando designação de audiência.'],
            ['processo_id' => $proc2, 'data' => now()->subDays(15)->toDateString(), 'descricao' => 'Citação realizada. Prazo de contestação iniciado.'],
        ];

        foreach ($andamentos as $a) {
            $jaExiste = DB::table('andamentos')
                ->where('tenant_id', $tid)
                ->where('processo_id', $a['processo_id'])
                ->where('descricao', $a['descricao'])
                ->exists();

            if (!$jaExiste) {
                DB::table('andamentos')->insert([
                    'tenant_id'   => $tid,
                    'processo_id' => $a['processo_id'],
                    'data'        => $a['data'],
                    'descricao'   => $a['descricao'],
                    'interno'     => false,
                    'usuario_id'  => $adminId,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        OnboardingService::inicializarParaTenant($tid);
    }

    // ── Lookups — NUNCA criam registros ──────────────────────────────

    private function buscarFase(int $tid): ?int
    {
        // 1. Fase do próprio tenant
        $id = DB::table('fases')->where('tenant_id', $tid)->orderBy('ordem')->value('id');
        if ($id) return $id;

        // 2. Fase global (tenant_id nulo)
        $id = DB::table('fases')->whereNull('tenant_id')->orderBy('ordem')->value('id');
        if ($id) return $id;

        // 3. Qualquer fase disponível
        return DB::table('fases')->orderBy('id')->value('id');
    }

    private function buscarRisco(int $tid): ?int
    {
        $id = DB::table('graus_risco')->where('tenant_id', $tid)->orderBy('id')->value('id');
        if ($id) return $id;

        $id = DB::table('graus_risco')->whereNull('tenant_id')->orderBy('id')->value('id');
        if ($id) return $id;

        return DB::table('graus_risco')->orderBy('id')->value('id');
    }

    private function buscarTipoAcao(int $tid, string $descricao): ?int
    {
        // Tenta match por descrição primeiro
        foreach ([
            fn($q) => $q->where('tenant_id', $tid)->where('descricao', $descricao),
            fn($q) => $q->whereNull('tenant_id')->where('descricao', $descricao),
            fn($q) => $q->where('descricao', $descricao),
            // Se não achou pelo nome, pega qualquer do tenant
            fn($q) => $q->where('tenant_id', $tid),
            fn($q) => $q->whereNull('tenant_id'),
            fn($q) => $q,
        ] as $scope) {
            $id = DB::table('tipos_acao')->tap($scope)->orderBy('id')->value('id');
            if ($id) return $id;
        }

        return null;
    }

    // ── Helpers de dados fictícios (idempotentes) ─────────────────────

    private function upsertPessoa(int $tid, array $dados, string $tipo, $now): int
    {
        $existing = DB::table('pessoas')
            ->where('tenant_id', $tid)
            ->where('cpf_cnpj', $dados['cpf_cnpj'])
            ->value('id');

        if ($existing) return $existing;

        $id = DB::table('pessoas')->insertGetId([
            'tenant_id'  => $tid,
            'ativo'      => true,
            'created_at' => $now,
            'updated_at' => $now,
            ...$dados,
        ]);

        // Tipo da pessoa (pessoa_tipos)
        $tipoExiste = DB::table('pessoa_tipos')
            ->where('pessoa_id', $id)
            ->where('tipo', $tipo)
            ->exists();

        if (!$tipoExiste) {
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

        if ($existing) return $existing;

        return DB::table('processos')->insertGetId([
            'tenant_id'  => $tid,
            'numero'     => $numero,
            'created_at' => $now,
            'updated_at' => $now,
            ...$dados,
        ]);
    }
}
