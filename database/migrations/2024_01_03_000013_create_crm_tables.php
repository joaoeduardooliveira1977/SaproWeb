<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_oportunidades', function (Blueprint $table) {
            $table->id();

            // Lead
            $table->string('nome', 150);
            $table->string('telefone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('cpf_cnpj', 18)->nullable();
            $table->string('origem', 30)->default('indicacao');
            // indicacao | site | redes_sociais | telefone | evento | outro

            // Oportunidade
            $table->string('titulo', 200)->nullable();
            $table->string('area_direito', 80)->nullable();
            $table->decimal('valor_estimado', 15, 2)->nullable();
            $table->text('descricao')->nullable();

            // Pipeline
            $table->string('etapa', 30)->default('novo_contato');
            // novo_contato | qualificacao | reuniao | proposta | negociacao | ganho | perdido

            $table->foreignId('responsavel_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->date('data_previsao')->nullable();
            $table->date('data_fechamento')->nullable();
            $table->string('motivo_perda', 200)->nullable();

            // Conversão
            $table->boolean('convertido')->default(false);
            $table->foreignId('pessoa_id')->nullable()->constrained('pessoas')->nullOnDelete();

            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->index('etapa');
            $table->index('responsavel_id');
        });

        Schema::create('crm_atividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oportunidade_id')->constrained('crm_oportunidades')->cascadeOnDelete();
            $table->string('tipo', 20)->default('tarefa');
            // ligacao | reuniao | email | whatsapp | tarefa
            $table->text('descricao');
            $table->date('data_prevista')->nullable();
            $table->date('data_realizada')->nullable();
            $table->boolean('concluida')->default(false);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->index(['oportunidade_id', 'concluida']);
            $table->index('data_prevista');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_atividades');
        Schema::dropIfExists('crm_oportunidades');
    }
};
