<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procuracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('pessoas')->onDelete('cascade');
            $table->foreignId('processo_id')->nullable()->constrained('processos')->onDelete('set null');
            $table->string('tipo', 60)->default('ad judicia'); // ad judicia, ad negotia, especial
            $table->date('data_emissao');
            $table->date('data_validade')->nullable();
            $table->text('poderes')->nullable();
            $table->string('arquivo')->nullable();
            $table->string('observacoes')->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procuracoes');
    }
};
