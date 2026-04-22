<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('numero', 20);                          // ORC-2026-001
            $table->foreignId('oportunidade_id')->nullable()->constrained('crm_oportunidades')->nullOnDelete();
            $table->foreignId('pessoa_id')->nullable()->constrained('pessoas')->nullOnDelete();

            // Destinatário (pode ser lead ainda não cadastrado)
            $table->string('nome_cliente', 150);
            $table->string('email_cliente', 150)->nullable();
            $table->string('telefone_cliente', 30)->nullable();

            // Objeto
            $table->string('titulo', 200);
            $table->string('area_direito', 80)->nullable();
            $table->text('descricao')->nullable();                  // descrição dos serviços
            $table->text('observacoes')->nullable();

            // Valores
            $table->string('tipo_honorario', 30)->default('fixo'); // fixo | percentual | hora | sucesso
            $table->decimal('valor', 15, 2)->default(0);
            $table->integer('parcelas')->default(1);
            $table->decimal('valor_parcela', 15, 2)->nullable();
            $table->decimal('percentual_exito', 5, 2)->nullable();
            $table->decimal('valor_hora', 15, 2)->nullable();

            // Validade e status
            $table->date('validade')->nullable();
            $table->string('status', 20)->default('rascunho');    // rascunho | enviado | aceito | recusado | expirado
            $table->date('data_resposta')->nullable();
            $table->text('motivo_recusa')->nullable();

            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index('oportunidade_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orcamentos');
    }
};
