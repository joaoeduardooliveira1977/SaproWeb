<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos_mensais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('cliente_id');
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->tinyInteger('dia_vencimento'); // 1–28
            $table->string('periodicidade', 20)->default('mensal'); // mensal,bimestral,trimestral,semestral,anual
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->string('status', 20)->default('ativo'); // ativo,suspenso,encerrado
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cliente_id')->references('id')->on('pessoas');
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'cliente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos_mensais');
    }
};
