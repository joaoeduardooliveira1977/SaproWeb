<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('tipo', 50); // prazo_vencendo | prazo_vencido | prazo_fatal | honorario_atrasado
            $table->string('titulo', 200);
            $table->text('mensagem')->nullable();
            $table->string('referencia_tipo', 50)->nullable(); // prazo | honorario_parcela
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->string('link', 200)->nullable();
            $table->boolean('lida')->default(false);
            $table->timestamps();

            $table->index(['usuario_id', 'lida']);
            $table->index(['referencia_tipo', 'referencia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
