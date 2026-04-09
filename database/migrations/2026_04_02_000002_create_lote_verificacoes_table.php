<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lote_verificacoes', function (Blueprint $table) {
            $table->id();
            $table->string('processo_numero');
            $table->enum('status', ['aguardando', 'verificando', 'verificado', 'erro'])->default('aguardando');
            $table->text('erro_mensagem')->nullable();
            $table->foreignId('user_id')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lote_verificacoes');
    }
};
