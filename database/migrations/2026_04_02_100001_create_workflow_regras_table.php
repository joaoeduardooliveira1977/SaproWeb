<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_regras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->string('nome');
            $table->string('descricao')->nullable();

            // Gatilho que dispara a regra
            // Valores: andamento.criado | processo.fase_mudou |
            //          prazo.vencendo | prazo.vencido | processo.sem_andamento_dias
            $table->string('gatilho');

            // Config extra do gatilho (ex: {"dias": 30} para sem_andamento_dias)
            $table->json('gatilho_config')->nullable();

            // Array de condições: [{campo, op, valor}]
            $table->json('condicoes')->default('[]');

            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('execucoes_total')->default(0);

            $table->timestamps();

            $table->index(['tenant_id', 'gatilho', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_regras');
    }
};
