<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobrancas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcela_id')->constrained('honorario_parcelas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('pessoas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->enum('tipo', ['email', 'ligacao', 'whatsapp', 'reuniao', 'negociacao', 'acordo']);
            $table->date('data');
            $table->text('descricao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobrancas');
    }
};
