<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receitas_processos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tenant_id')->index();
            $table->foreignId('cliente_id')->constrained('pessoas');
            $table->decimal('valor_unitario', 10, 2);
            $table->tinyInteger('dia_vencimento');
            $table->date('data_inicio');
            $table->string('status', 20)->default('ativo'); // ativo, suspenso, encerrado
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'cliente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receitas_processos');
    }
};
