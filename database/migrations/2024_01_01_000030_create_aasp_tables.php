<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aasp_advogados', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('codigo_aasp', 20)->unique();
            $table->string('chave_aasp', 100);
            $table->string('email', 150)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('aasp_publicacoes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_aasp', 20)->index();
            $table->date('data')->nullable();
            $table->string('jornal', 100)->nullable();
            $table->string('numero_processo', 100)->nullable();
            $table->string('titulo', 500)->nullable();
            $table->text('texto')->nullable();
            $table->string('numero_publicacao', 100)->nullable()->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('aasp_config', function (Blueprint $table) {
            $table->id();
            $table->text('emails_destino')->nullable();
            $table->string('horario_rotina', 5)->default('08:00');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aasp_config');
        Schema::dropIfExists('aasp_publicacoes');
        Schema::dropIfExists('aasp_advogados');
    }
};
