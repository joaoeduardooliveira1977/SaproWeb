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
        $tid = $this->tenantId;
        $adminId = $this->adminUserId;

        // Já tem dados? Não popular de novo.
        if (DB::table('pessoas')->where('tenant_id', $tid)->count() > 0) return;

        // ── Fases ────────────────────────────────────────────────────
        $faseId = $this->garantirFase($tid);

        // ── Grau de risco ────────────────────────────────────────────
        $riscoId = $this->garantirRisco($tid);

        // ── Tipo de ação ─────────────────────────────────────────────
        $tipoTrabId  = $this->garantirTipoAcao($tid, 'Trabalhista');
        $tipoCivelId = $this->garantirTipoAcao($tid, 'Cível');

        // ── Pessoas ──────────────────────────────────────────────────
        $now = now();

        $p1 = DB::table('pessoas')->insertGetId([
            'tenant_id'       => $tid,
            'nome'            => 'João Carlos Silva',
            'tipo_pessoa'     => 'fisica',
            'cpf_cnpj'        => '111.222.333-96',
            'telefone'        => '(11) 91234-5678',
            'cidade'          => 'São Paulo',
            'estado'          => 'SP',
            'ativo'           => true,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
        DB::table('pessoa_tipos')->insert(['pessoa_id' => $p1, 'tipo' => 'Cliente']);

        $p2 = DB::table('pessoas')->insertGetId([
            'tenant_id'       => $tid,
            'nome'            => 'Maria Fernanda Costa',
            'tipo_pessoa'     => 'fisica',
            'cpf_cnpj'        => '222.333.444-07',
            'telefone'        => '(11) 92345-6789',
            'cidade'          => 'Campinas',
            'estado'          => 'SP',
            'ativo'           => true,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
        DB::table('pessoa_tipos')->insert(['pessoa_id' => $p2, 'tipo' => 'Cliente']);

        $p3 = DB::table('pessoas')->insertGetId([
            'tenant_id'       => $tid,
            'nome'            => 'Construtora Horizonte Ltda',
            'tipo_pessoa'     => 'juridica',
            'cpf_cnpj'        => '11.222.333/0001-81',
            'telefone'        => '(11) 3456-7890',
            'cidade'          => 'São Paulo',
            'estado'          => 'SP',
            'ativo'           => true,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
        DB::table('pessoa_tipos')->insert(['pessoa_id' => $p3, 'tipo' => 'Cliente']);

        $p4 = DB::table('pessoas')->insertGetId([
            'tenant_id'       => $tid,
            'nome'            => 'Pedro Almeida Santos',
            'tipo_pessoa'     => 'fisica',
            'cpf_cnpj'        => '333.444.555-18',
            'telefone'        => '(11) 93456-7890',
            'cidade'          => 'Guarulhos',
            'estado'          => 'SP',
            'ativo'           => true,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
        DB::table('pessoa_tipos')->insert(['pessoa_id' => $p4, 'tipo' => 'Parte Contrária']);

        // ── Processos ─────────────────────────────────────────────────
        $proc1 = DB::table('processos')->insertGetId([
            'tenant_id'         => $tid,
            'numero'            => '0001234-55.2024.5.02.0001',
            'cliente_id'        => $p1,
            'parte_contraria'   => 'Construtora Horizonte Ltda',
            'parte_contraria_id'=> $p3,
            'advogado_id'       => $p1, // placeholder
            'tipo_acao_id'      => $tipoTrabId,
            'fase_id'           => $faseId,
            'risco_id'          => $riscoId,
            'extrajudicial'     => false,
            'autor_reu'         => 'Autor',
            'valor_causa'       => 45000.00,
            'status'            => 'Ativo',
            'criado_por'        => $adminId,
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);

        $proc2 = DB::table('processos')->insertGetId([
            'tenant_id'         => $tid,
            'numero'            => '0009876-11.2024.8.26.0100',
            'cliente_id'        => $p2,
            'parte_contraria'   => 'Pedro Almeida Santos',
            'parte_contraria_id'=> $p4,
            'advogado_id'       => $p2, // placeholder
            'tipo_acao_id'      => $tipoCivelId,
            'fase_id'           => $faseId,
            'risco_id'          => $riscoId,
            'extrajudicial'     => false,
            'autor_reu'         => 'Autor',
            'valor_causa'       => 15000.00,
            'status'            => 'Ativo',
            'criado_por'        => $adminId,
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);

        $proc3 = DB::table('processos')->insertGetId([
            'tenant_id'     => $tid,
            'numero'        => 'EXT-2024-001',
            'cliente_id'    => $p3,
            'extrajudicial' => true,
            'autor_reu'     => 'Requerente',
            'valor_causa'   => 8000.00,
            'status'        => 'Ativo',
            'criado_por'    => $adminId,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        // ── Prazos ────────────────────────────────────────────────────
        $prazos = [
            [
                'titulo'       => 'Apresentar contestação',
                'tipo'         => 'Prazo Fatal',
                'processo_id'  => $proc1,
                'data_prazo'   => now()->addDays(7)->toDateString(),
                'data_inicio'  => now()->toDateString(),
                'dias'         => 7,
                'prazo_fatal'  => true,
            ],
            [
                'titulo'       => 'Audiência de instrução',
                'tipo'         => 'Audiência',
                'processo_id'  => $proc2,
                'data_prazo'   => now()->addDays(15)->toDateString(),
                'data_inicio'  => now()->toDateString(),
                'dias'         => 15,
                'prazo_fatal'  => false,
            ],
            [
                'titulo'       => 'Recurso de apelação',
                'tipo'         => 'Prazo',
                'processo_id'  => $proc1,
                'data_prazo'   => now()->addDays(30)->toDateString(),
                'data_inicio'  => now()->toDateString(),
                'dias'         => 30,
                'prazo_fatal'  => false,
            ],
        ];

        foreach ($prazos as $prazo) {
            DB::table('prazos')->insert([
                'tenant_id'      => $tid,
                'processo_id'    => $prazo['processo_id'],
                'titulo'         => $prazo['titulo'],
                'tipo'           => $prazo['tipo'],
                'data_inicio'    => $prazo['data_inicio'],
                'tipo_contagem'  => 'corridos',
                'dias'           => $prazo['dias'],
                'data_prazo'     => $prazo['data_prazo'],
                'prazo_fatal'    => $prazo['prazo_fatal'],
                'status'         => 'aberto',
                'criado_por'     => $adminId,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        // ── Andamentos ────────────────────────────────────────────────
        DB::table('andamentos')->insert([
            [
                'tenant_id'  => $tid,
                'processo_id'=> $proc1,
                'data'       => now()->subDays(30)->toDateString(),
                'descricao'  => 'Petição inicial protocolada. Aguardando designação de audiência.',
                'interno'    => false,
                'usuario_id' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tenant_id'  => $tid,
                'processo_id'=> $proc2,
                'data'       => now()->subDays(15)->toDateString(),
                'descricao'  => 'Citação realizada. Prazo de contestação iniciado.',
                'interno'    => false,
                'usuario_id' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        OnboardingService::inicializarParaTenant($tid);
    }

    // ── Helpers internos ─────────────────────────────────────────────

    private function garantirFase(int $tid): ?int
    {
        // Tenta fase do tenant, depois qualquer fase global
        $fase = DB::table('fases')
            ->where(fn($q) => $q->where('tenant_id', $tid)->orWhereNull('tenant_id'))
            ->orderBy('id')
            ->value('id');

        if ($fase) return $fase;

        // Cria fase básica para este tenant
        return DB::table('fases')->insertGetId([
            'tenant_id'  => $tid,
            'descricao'  => 'Conhecimento',
            'codigo'     => 'CON',
            'ordem'      => 1,
            'ativo'      => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function garantirRisco(int $tid): ?int
    {
        $risco = DB::table('graus_risco')
            ->where(fn($q) => $q->where('tenant_id', $tid)->orWhereNull('tenant_id'))
            ->orderBy('id')
            ->value('id');

        if ($risco) return $risco;

        return DB::table('graus_risco')->insertGetId([
            'tenant_id'  => $tid,
            'descricao'  => 'Médio',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function garantirTipoAcao(int $tid, string $descricao): ?int
    {
        $tipo = DB::table('tipos_acao')
            ->where(fn($q) => $q->where('tenant_id', $tid)->orWhereNull('tenant_id'))
            ->where('descricao', $descricao)
            ->value('id');

        if ($tipo) return $tipo;

        // Qualquer tipo disponível
        $tipo = DB::table('tipos_acao')
            ->where(fn($q) => $q->where('tenant_id', $tid)->orWhereNull('tenant_id'))
            ->value('id');

        if ($tipo) return $tipo;

        return DB::table('tipos_acao')->insertGetId([
            'tenant_id'  => $tid,
            'descricao'  => $descricao,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
