<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assinaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->nullable()->constrained('documentos')->nullOnDelete();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->foreignId('criado_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('arquivo_path')->nullable();     // path no storage (se upload direto)
            $table->string('arquivo_nome')->nullable();     // nome amigável
            $table->string('clicksign_document_key', 100)->nullable()->index();
            $table->string('clicksign_list_key', 100)->nullable()->index();
            $table->enum('status', [
                'rascunho', 'enviado', 'assinando', 'concluido', 'recusado', 'cancelado', 'erro'
            ])->default('rascunho');
            $table->timestamp('deadline_at')->nullable();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamp('concluido_em')->nullable();
            $table->text('erro_mensagem')->nullable();
            $table->timestamps();
        });

        Schema::create('assinatura_signatarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assinatura_id')->constrained('assinaturas')->cascadeOnDelete();
            $table->string('nome');
            $table->string('email');
            $table->string('cpf', 14)->nullable();
            $table->string('celular', 20)->nullable();
            $table->enum('papel', [
                'assinar', 'assinar_como_testemunha', 'aprovar',
                'reconhecer', 'rubricar', 'assinar_como_parte'
            ])->default('assinar');
            $table->enum('auth', ['email', 'sms', 'whatsapp', 'pix'])->default('email');
            $table->string('clicksign_signer_key', 100)->nullable();
            $table->enum('status', ['pendente', 'enviado', 'assinado', 'recusado'])->default('pendente');
            $table->timestamp('assinado_em')->nullable();
            $table->integer('ordem')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assinatura_signatarios');
        Schema::dropIfExists('assinaturas');
    }
};
