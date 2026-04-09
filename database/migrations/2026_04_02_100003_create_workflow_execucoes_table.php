<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_execucoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->foreignId('regra_id')->constrained('workflow_regras')->cascadeOnDelete();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();

            // Dados do evento que disparou (andamento_id, fase_id, etc.)
            $table->json('gatilho_payload')->nullable();

            // executado | erro | ignorado (condições não satisfeitas)
            $table->string('status')->default('executado');

            // O que foi criado/feito (prazo_id, notificacao_id, etc.)
            $table->json('resultado')->nullable();

            $table->text('erro_mensagem')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'regra_id']);
            $table->index(['tenant_id', 'processo_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_execucoes');
    }
};
