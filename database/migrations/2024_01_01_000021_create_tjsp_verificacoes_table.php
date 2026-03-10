<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tjsp_verificacoes', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pendente'); // pendente, rodando, concluido, erro
            $table->integer('total')->default(0);
            $table->integer('processado')->default(0);
            $table->string('processo_atual')->nullable();
            $table->integer('novos_total')->default(0);
            $table->json('novos_andamentos')->nullable(); // processos com andamentos novos
            $table->timestamp('iniciado_em')->nullable();
            $table->timestamp('concluido_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tjsp_verificacoes');
    }
};
