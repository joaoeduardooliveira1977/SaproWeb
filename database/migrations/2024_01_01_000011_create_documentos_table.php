<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('pessoas')->nullOnDelete();
            $table->enum('tipo', ['peticao','contrato','procuracao','laudo','documento_cliente','sentenca','outro']);
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('arquivo'); // caminho no disco
            $table->string('arquivo_original'); // nome original
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('tamanho')->nullable(); // bytes
            $table->date('data_documento')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
