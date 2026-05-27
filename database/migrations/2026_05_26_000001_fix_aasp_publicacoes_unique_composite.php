<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Corrige a idempotência das publicações AASP.
 *
 * Problema: o índice UNIQUE simples em numero_publicacao descartava a publicação
 * quando um segundo advogado estava no mesmo processo (mesma numeração).
 *
 * Solução: substituir pelo índice UNIQUE composto (numero_publicacao, codigo_aasp),
 * permitindo que a mesma publicação seja salva uma vez por advogado.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aasp_publicacoes', function (Blueprint $table) {
            // Remove o índice UNIQUE simples gerado pela convenção do Laravel
            // (nome padrão: aasp_publicacoes_numero_publicacao_unique)
            $table->dropUnique(['numero_publicacao']);

            // Adiciona o índice UNIQUE composto
            $table->unique(['numero_publicacao', 'codigo_aasp'], 'aasp_publicacoes_numpub_codaasp_unique');
        });
    }

    public function down(): void
    {
        Schema::table('aasp_publicacoes', function (Blueprint $table) {
            $table->dropUnique('aasp_publicacoes_numpub_codaasp_unique');

            // Restaura o índice original
            $table->unique(['numero_publicacao'], 'aasp_publicacoes_numero_publicacao_unique');
        });
    }
};
