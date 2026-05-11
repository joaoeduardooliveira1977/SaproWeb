<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financeiro_lancamento_anexos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lancamento_id');
            $table->unsignedBigInteger('tenant_id');
            $table->string('arquivo');
            $table->string('arquivo_original');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('tamanho')->nullable();
            $table->string('uploaded_by', 150)->nullable();
            $table->timestamps();

            $table->foreign('lancamento_id')
                  ->references('id')
                  ->on('financeiro_lancamentos')
                  ->onDelete('cascade');

            $table->index(['lancamento_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financeiro_lancamento_anexos');
    }
};
