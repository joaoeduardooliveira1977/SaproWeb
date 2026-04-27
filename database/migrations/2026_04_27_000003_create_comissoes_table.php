<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comissoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('indicador_id')->constrained('indicadores')->cascadeOnDelete();
            $table->foreignId('pessoa_id')->constrained('pessoas')->cascadeOnDelete();
            $table->string('origem_tipo', 50);   // recebimento | honorario_parcela
            $table->unsignedBigInteger('origem_id');
            $table->decimal('valor_base', 12, 2);
            $table->decimal('percentual', 5, 2);
            $table->decimal('valor_comissao', 12, 2);
            $table->date('competencia');          // primeiro dia do mês
            $table->string('status', 20)->default('pendente'); // pendente | pago
            $table->date('data_pagamento')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['origem_tipo', 'origem_id']); // evita duplicata por origem
            $table->index(['indicador_id', 'competencia', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comissoes');
    }
};
