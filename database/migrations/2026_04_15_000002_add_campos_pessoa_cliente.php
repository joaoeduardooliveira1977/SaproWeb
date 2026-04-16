<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->enum('tipo_pessoa', ['fisica', 'juridica'])->default('fisica')->after('nome');
            $table->string('inscricao_estadual', 30)->nullable()->after('rg'); // IE para PJ
            // Documento de validação (contrato assinado) — visível só a admin/financeiro
            $table->string('contrato_arquivo', 500)->nullable()->after('observacoes');
            $table->string('contrato_arquivo_original', 300)->nullable()->after('contrato_arquivo');
            $table->timestamp('contrato_validado_em')->nullable()->after('contrato_arquivo_original');
            $table->string('contrato_validado_por', 100)->nullable()->after('contrato_validado_em');
        });
    }

    public function down(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_pessoa', 'inscricao_estadual',
                'contrato_arquivo', 'contrato_arquivo_original',
                'contrato_validado_em', 'contrato_validado_por',
            ]);
        });
    }
};
