<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicados', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('mensagem');
            $table->enum('tipo', ['info', 'aviso', 'critico'])->default('info');
            $table->boolean('ativo')->default(true);
            $table->timestamp('expira_em')->nullable();
            $table->unsignedBigInteger('criado_por')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicados');
    }
};
