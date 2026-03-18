<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ofx_importacoes', function (Blueprint $table) {
            $table->id();
            $table->string('arquivo', 200);
            $table->string('banco', 100)->nullable();
            $table->string('agencia', 30)->nullable();
            $table->string('conta', 60)->nullable();
            $table->date('data_ini')->nullable();
            $table->date('data_fim')->nullable();
            $table->unsignedInteger('total_lancamentos')->default(0);
            $table->unsignedInteger('conciliados')->default(0);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('ofx_lancamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('importacao_id')->constrained('ofx_importacoes')->cascadeOnDelete();
            $table->date('data');
            $table->decimal('valor', 15, 2); // positivo=crédito, negativo=débito
            $table->string('tipo', 20)->nullable();  // CREDIT, DEBIT, etc.
            $table->string('descricao', 500)->nullable();
            $table->string('fitid', 150)->nullable();
            $table->boolean('conciliado')->default(false);
            $table->string('referencia_tipo', 20)->nullable(); // pagamentos | recebimentos
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->timestamps();

            $table->index(['importacao_id', 'conciliado']);
            $table->index('data');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ofx_lancamentos');
        Schema::dropIfExists('ofx_importacoes');
    }
};
