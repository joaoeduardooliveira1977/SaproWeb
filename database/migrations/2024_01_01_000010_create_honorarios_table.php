<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('honorarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->foreignId('cliente_id')->constrained('pessoas')->cascadeOnDelete();
            $table->enum('tipo', ['fixo_mensal', 'exito', 'hora', 'ato_diligencia']);
            $table->string('descricao');
            $table->decimal('valor_contrato', 12, 2);
            $table->decimal('percentual_exito', 5, 2)->nullable(); // % sobre valor causa
            $table->integer('total_parcelas')->default(1);
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->enum('status', ['ativo', 'encerrado', 'suspenso'])->default('ativo');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('honorario_parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('honorario_id')->constrained('honorarios')->cascadeOnDelete();
            $table->integer('numero_parcela');
            $table->decimal('valor', 12, 2);
            $table->date('vencimento');
            $table->date('data_pagamento')->nullable();
            $table->decimal('valor_pago', 12, 2)->nullable();
            $table->enum('status', ['pendente', 'pago', 'atrasado', 'cancelado'])->default('pendente');
            $table->string('forma_pagamento')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('honorario_parcelas');
        Schema::dropIfExists('honorarios');
    }
};
