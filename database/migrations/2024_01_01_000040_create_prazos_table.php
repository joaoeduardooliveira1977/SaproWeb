<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prazos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->foreignId('responsavel_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('criado_por')->nullable()->constrained('usuarios')->nullOnDelete();

            $table->string('titulo', 200);
            $table->text('descricao')->nullable();
            $table->string('tipo', 50)->default('Prazo'); // Prazo | Prazo Fatal | Audiência | Diligência | Recurso

            $table->date('data_inicio');
            $table->string('tipo_contagem', 10)->default('corridos'); // corridos | uteis
            $table->unsignedSmallInteger('dias')->nullable();
            $table->date('data_prazo');

            $table->boolean('prazo_fatal')->default(false);
            $table->string('status', 20)->default('aberto'); // aberto | cumprido | perdido
            $table->date('data_cumprimento')->nullable();
            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prazos');
    }
};
