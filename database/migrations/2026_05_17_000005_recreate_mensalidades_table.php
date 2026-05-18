<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('mensalidades');

        Schema::create('mensalidades', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tenant_id')->index();
            $table->foreignId('contrato_mensal_id')
                  ->constrained('contratos_mensais')
                  ->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('pessoas');
            $table->unsignedBigInteger('financeiro_lancamento_id')->nullable();
            $table->string('competencia', 7);   // YYYY-MM
            $table->date('vencimento');
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_recebido', 10, 2)->default(0);
            $table->string('status', 20)->default('pendente');
            // valores: pendente, recebido, parcial, vencido, cancelado
            $table->date('data_recebimento')->nullable();
            $table->string('forma_pagamento', 30)->nullable();
            $table->boolean('notificado_5dias')->default(false);
            $table->boolean('notificado_3dias')->default(false);
            $table->boolean('notificado_vencimento')->default(false);
            $table->boolean('notificado_atraso')->default(false);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['contrato_mensal_id', 'competencia']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'vencimento']);
            $table->index(['tenant_id', 'cliente_id']);
        });

        // FK para financeiro_lancamentos adicionada após a tabela ser criada
        Schema::table('mensalidades', function (Blueprint $table) {
            $table->foreign('financeiro_lancamento_id')
                  ->references('id')->on('financeiro_lancamentos')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensalidades');
    }
};
