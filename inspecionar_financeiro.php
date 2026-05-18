<?php
// Coloque este arquivo na raiz do projeto SAPRO e rode:
// php inspecionar_financeiro.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$keywords = ['financ', 'lancament', 'receita', 'caixa', 'moviment', 'pagament', 'contrato'];

$tables = DB::select("
    SELECT table_name 
    FROM information_schema.tables 
    WHERE table_schema = 'public' 
    ORDER BY table_name
");

echo "\n=== TABELAS RELACIONADAS AO FINANCEIRO ===\n";

foreach ($tables as $t) {
    $nome = $t->table_name;
    $match = false;
    foreach ($keywords as $kw) {
        if (stripos($nome, $kw) !== false) { $match = true; break; }
    }
    if (!$match) continue;

    echo "\n--- {$nome} ---\n";
    $cols = DB::select("
        SELECT column_name, data_type, is_nullable, column_default
        FROM information_schema.columns 
        WHERE table_schema = 'public' AND table_name = '{$nome}'
        ORDER BY ordinal_position
    ");
    foreach ($cols as $c) {
        $nullable = $c->is_nullable === 'YES' ? ' (nullable)' : '';
        $default  = $c->column_default ? " [default: {$c->column_default}]" : '';
        echo "  {$c->column_name} | {$c->data_type}{$nullable}{$default}\n";
    }
}

echo "\n=== FIM ===\n";
