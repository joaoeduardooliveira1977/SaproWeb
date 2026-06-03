<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('despesas_reembolsos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('cliente_id');
            $table->date('data_lancamento');
            $table->enum('tipo', ['despesa', 'reembolso']);
            $table->string('descricao', 300);
            $table->decimal('valor', 10, 2);
            $table->enum('status', ['pendente', 'aprovado', 'nao_cobrar', 'ja_cobrado'])->default('pendente');
            $table->unsignedBigInteger('responsavel_id');
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('aprovado_por')->nullable();
            $table->timestamp('aprovado_em')->nullable();
            $table->unsignedBigInteger('financeiro_id')->nullable();
            $table->string('financeiro_tipo', 20)->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('cliente_id')->references('id')->on('pessoas')->onDelete('restrict');
            $table->foreign('responsavel_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('aprovado_por')->references('id')->on('usuarios')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('despesas_reembolsos_anexos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('despesa_reembolso_id');
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('nome_original', 255);
            $table->string('caminho', 500);
            $table->enum('tipo_documento', ['guia', 'comprovante', 'nota_fiscal', 'darf', 'gru', 'recibo', 'outro'])->default('outro');
            $table->unsignedInteger('tamanho')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('criado_por');
            $table->foreign('despesa_reembolso_id')->references('id')->on('despesas_reembolsos')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('criado_por')->references('id')->on('usuarios')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('despesas_reembolsos_anexos');
        Schema::dropIfExists('despesas_reembolsos');
    }
};
