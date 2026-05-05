<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DominioSeeder extends Seeder
{
    public function run(): void
    {
        // ── Origens de Recebimento (padrão global) ─────────────────────────
        $origens = [
            'Honorários',
            'Reembolso de Custas',
            'Contrato Recorrente',
            'Serviço Avulso',
            'Honorários de Êxito',
        ];

        foreach ($origens as $descricao) {
            DB::table('origens_recebimento')->updateOrInsert(
                ['descricao' => $descricao, 'tenant_id' => null],
                ['ativo' => true]
            );
        }

        // ── Graus de Risco (padrão global) ────────────────────────────────
        $riscos = [
            ['codigo' => 'BAI', 'descricao' => 'Baixo',       'cor_hex' => '#16a34a'],
            ['codigo' => 'MED', 'descricao' => 'Médio',       'cor_hex' => '#d97706'],
            ['codigo' => 'ALT', 'descricao' => 'Alto',        'cor_hex' => '#dc2626'],
            ['codigo' => 'IMP', 'descricao' => 'Improvável',  'cor_hex' => '#64748b'],
        ];

        foreach ($riscos as $r) {
            DB::table('graus_risco')->updateOrInsert(
                ['codigo' => $r['codigo'], 'tenant_id' => null],
                ['descricao' => $r['descricao'], 'cor_hex' => $r['cor_hex']]
            );
        }

        // ── Tipos de Ação (padrão global) ─────────────────────────────────
        $tiposAcao = [
            ['codigo' => 'TRA', 'descricao' => 'Trabalhista'],
            ['codigo' => 'CIV', 'descricao' => 'Cível'],
            ['codigo' => 'PRE', 'descricao' => 'Previdenciário'],
            ['codigo' => 'TRI', 'descricao' => 'Tributário'],
            ['codigo' => 'FAM', 'descricao' => 'Família'],
            ['codigo' => 'PEN', 'descricao' => 'Penal'],
            ['codigo' => 'ADM', 'descricao' => 'Administrativo'],
            ['codigo' => 'CON', 'descricao' => 'Consumidor'],
        ];

        foreach ($tiposAcao as $t) {
            DB::table('tipos_acao')->updateOrInsert(
                ['codigo' => $t['codigo'], 'tenant_id' => null],
                ['descricao' => $t['descricao'], 'ativo' => true]
            );
        }

        // ── Tipos de Processo (padrão global) ─────────────────────────────
        $tiposProcesso = [
            ['codigo' => 'ORD', 'descricao' => 'Ordinário'],
            ['codigo' => 'SUM', 'descricao' => 'Sumário'],
            ['codigo' => 'CAU', 'descricao' => 'Cautelar'],
            ['codigo' => 'EXE', 'descricao' => 'Execução'],
            ['codigo' => 'REC', 'descricao' => 'Recurso'],
            ['codigo' => 'MAN', 'descricao' => 'Mandado de Segurança'],
        ];

        foreach ($tiposProcesso as $t) {
            DB::table('tipos_processo')->updateOrInsert(
                ['codigo' => $t['codigo'], 'tenant_id' => null],
                ['descricao' => $t['descricao'], 'ativo' => true]
            );
        }

        $this->command->info('✅ Dados de domínio padrão (tenant_id = null) inseridos/atualizados!');
    }
}
