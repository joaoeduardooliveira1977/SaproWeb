<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * SAPRO — Módulo Financeiro
 * Tabelas: apontamentos, pagamentos, recebimentos
 * Migradas do banco Access (APONTAMENTOS, PAGAMENTOS, RECEBIMENTOS)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Fornecedores ───────────────────────────
        // Origem: tabela FORNECEDORES do Access
        Schema::create('fornecedores', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('cnpj_cpf', 18)->nullable()->unique();
            $table->string('telefone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // ── Origens de Recebimento ─────────────────
        // Origem: tabela ORIGEM do Access
        Schema::create('origens_recebimento', function (Blueprint $table) {
            $table->id();
            $table->string('descricao', 100)->unique();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // ── Apontamentos de Horas ──────────────────
        // Origem: tabela APONTAMENTOS do Access
        // Registro de horas trabalhadas por advogado em cada processo
        Schema::create('apontamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->foreignId('advogado_id')->nullable()->constrained('pessoas')->nullOnDelete();
            $table->date('data')->default(DB::raw('CURRENT_DATE'));
            $table->text('descricao');
            $table->decimal('horas', 6, 2)->default(0);    // horas trabalhadas
            $table->decimal('valor', 15, 2)->default(0);   // valor cobrado
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->index(['processo_id', 'data']);
        });

        // ── Pagamentos ─────────────────────────────
        // Origem: tabela PAGAMENTOS do Access
        // Despesas e pagamentos realizados pelo escritório
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->nullOnDelete();
            $table->date('data')->default(DB::raw('CURRENT_DATE'));
            $table->string('numero_doc', 60)->nullable();       // número do documento/NF
            $table->string('documento', 100)->nullable();       // tipo de documento
            $table->string('descricao', 200);
            $table->decimal('valor', 15, 2)->default(0);        // valor original
            $table->decimal('valor_pago', 15, 2)->default(0);   // valor efetivamente pago
            $table->date('data_vencimento')->nullable();
            $table->date('data_pagamento')->nullable();
            $table->boolean('pago')->default(false);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->index(['processo_id', 'data']);
            $table->index(['data_vencimento']);
        });

        // ── Recebimentos ───────────────────────────
        // Origem: tabela RECEBIMENTOS do Access
        // Valores recebidos de clientes por processo
        Schema::create('recebimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->foreignId('origem_id')->nullable()->constrained('origens_recebimento')->nullOnDelete();
            $table->date('data')->default(DB::raw('CURRENT_DATE'));
            $table->string('numero_doc', 60)->nullable();
            $table->string('documento', 100)->nullable();
            $table->string('descricao', 200)->nullable();
            $table->decimal('valor', 15, 2)->default(0);            // valor previsto
            $table->decimal('valor_recebido', 15, 2)->default(0);   // valor efetivamente recebido
            $table->date('data_recebimento')->nullable();
            $table->boolean('recebido')->default(false);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->index(['processo_id', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recebimentos');
        Schema::dropIfExists('pagamentos');
        Schema::dropIfExists('apontamentos');
        Schema::dropIfExists('origens_recebimento');
        Schema::dropIfExists('fornecedores');
    }
};
