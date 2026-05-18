<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensalidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('contrato_mensal_id');
            $table->unsignedBigInteger('cliente_id');
            $table->string('competencia', 7); // "2026-05"
            $table->date('vencimento');
            $table->decimal('valor', 10, 2);
            $table->string('status', 20)->default('pendente'); // pendente,pago,atrasado,cancelado
            $table->date('data_pagamento')->nullable();
            $table->decimal('valor_pago', 10, 2)->nullable();
            $table->string('forma_pagamento', 30)->nullable(); // pix,boleto,transferencia,dinheiro,cartao
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contrato_mensal_id')->references('id')->on('contratos_mensais');
            $table->foreign('cliente_id')->references('id')->on('pessoas');
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'competencia']);
            $table->index(['tenant_id', 'vencimento']);
            $table->index(['tenant_id', 'cliente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensalidades');
    }
};
