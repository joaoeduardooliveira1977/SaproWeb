<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_acoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regra_id')->constrained('workflow_regras')->cascadeOnDelete();

            // Ordem de execução dentro da regra
            $table->unsignedTinyInteger('ordem')->default(1);

            // Tipo da ação
            // Valores: criar_prazo | criar_notificacao | criar_agenda |
            //          enviar_whatsapp | atualizar_score | chamar_ia
            $table->string('tipo');

            // Parâmetros específicos do tipo (ver docs de cada AcaoXxx)
            $table->json('config')->default('{}');

            $table->timestamps();

            $table->index(['regra_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_acoes');
    }
};
