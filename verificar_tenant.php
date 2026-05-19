<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tabelas = [
    // Models sem Trait — verificar se têm tenant_id
    'honorario_parcelas',
    'contrato_servicos',
    'contrato_repasses',
    'crm_atividades',
    'correspondentes',
    'ofx_lancamentos',
    'ofx_importacoes',
    'auditorias',
    'assinatura_signatarios',
    'fases',
    'dominios',
    'assuntos',
    'reparticoes',
    'secretarias',
    'administradoras',
    'workflow_acoes',
    'lote_verificacoes',
    'tjsp_verificacoes',
    'processo_jurisprudencias',
    'indices_monetarios',
    'aasp_advogados',
    'aasp_publicacoes',
    'aasp_configs',
    'modulos',
    'usuarios',
    // Financeiro
    'financeiros',
];

echo "\n=== VERIFICAÇÃO DE tenant_id ===\n\n";

$comTenant   = [];
$semTenant   = [];
$naoExiste   = [];

foreach ($tabelas as $tabela) {
    $existe = DB::select("SELECT to_regclass('public.{$tabela}') as t");
    if (!$existe[0]->t) {
        $naoExiste[] = $tabela;
        continue;
    }

    $cols = DB::select("
        SELECT column_name FROM information_schema.columns
        WHERE table_schema = 'public'
        AND table_name = '{$tabela}'
        AND column_name = 'tenant_id'
    ");

    if (count($cols) > 0) {
        $comTenant[] = $tabela;
    } else {
        $semTenant[] = $tabela;
    }
}

echo "🔴 TEM tenant_id mas SEM Trait (RISCO - precisam do Trait):\n";
foreach ($comTenant as $t) echo "   - {$t}\n";

echo "\n✅ SEM tenant_id (tabelas globais - OK não ter Trait):\n";
foreach ($semTenant as $t) echo "   - {$t}\n";

echo "\n⚪ Tabela não existe no banco:\n";
foreach ($naoExiste as $t) echo "   - {$t}\n";

echo "\n=== FIM ===\n";
