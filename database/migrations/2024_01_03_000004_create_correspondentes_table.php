<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('correspondentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->foreignId('advogado_id')->constrained('pessoas')->cascadeOnDelete();
            $table->foreignId('solicitado_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('comarca', 150);
            $table->char('estado', 2)->nullable();
            $table->enum('tipo', ['audiencia', 'protocolo', 'citacao', 'pericia', 'diligencia', 'outro'])->default('diligencia');
            $table->text('descricao');
            $table->date('data_solicitacao');
            $table->date('data_prazo')->nullable();
            $table->date('data_realizado')->nullable();
            $table->decimal('valor_combinado', 12, 2)->nullable();
            $table->decimal('valor_pago', 12, 2)->nullable();
            $table->date('data_pagamento')->nullable();
            $table->enum('status', ['pendente', 'aceito', 'realizado', 'pago', 'cancelado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correspondentes');
    }
};
