<?php

namespace Database\Seeders;

use App\Models\{Modulo, Tenant};
use App\Services\ModuloService;
use Illuminate\Database\Seeder;

class ModulosSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Inserir/garantir todos os módulos do sistema ──────────────
        $modulos = [
            ['chave' => 'processos',           'nome' => 'Processos',             'ordem' => 1],
            ['chave' => 'pessoas',             'nome' => 'Pessoas / Clientes',    'ordem' => 2],
            ['chave' => 'prazos',              'nome' => 'Prazos',                'ordem' => 3],
            ['chave' => 'agenda',              'nome' => 'Agenda',                'ordem' => 4],
            ['chave' => 'financeiro',          'nome' => 'Financeiro',            'ordem' => 5],
            ['chave' => 'mensais',             'nome' => 'Mensais',               'ordem' => 6],
            ['chave' => 'relatorios',          'nome' => 'Relatórios',            'ordem' => 7],
            ['chave' => 'ferramentas',         'nome' => 'Ferramentas',           'ordem' => 8],
            ['chave' => 'aasp',                'nome' => 'Publicações AASP',      'ordem' => 9],
            ['chave' => 'despesas_reembolsos', 'nome' => 'Despesas / Reembolsos', 'ordem' => 10],
            ['chave' => 'assistente_ia',       'nome' => 'Assistente IA',         'ordem' => 11],
            ['chave' => 'portal_cliente',      'nome' => 'Portal do Cliente',     'ordem' => 12],
            ['chave' => 'pipeline_crm',        'nome' => 'Pipeline / CRM',        'ordem' => 13],
            ['chave' => 'automacao',           'nome' => 'Automação',             'ordem' => 14],
            ['chave' => 'cadastros',           'nome' => 'Cadastros Auxiliares',  'ordem' => 15],
            ['chave' => 'admin',               'nome' => 'Administração',         'ordem' => 16],
        ];

        foreach ($modulos as $dados) {
            Modulo::firstOrCreate(['chave' => $dados['chave']], array_merge($dados, ['ativo' => true]));
        }

        // ── 2. Popular plano_modulos ──────────────────────────────────────
        ModuloService::seedPlanoModulos();

        // ── 3. Inicializar módulos para tenants existentes ────────────────
        Tenant::withTrashed()->each(function (Tenant $tenant) {
            ModuloService::inicializarModulos($tenant);
        });

        $this->command->info('Módulos criados e inicializados para todos os tenants.');
    }
}
