<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes_whatsapp', function (Blueprint $table) {
            $table->id();
            $table->enum('canal', ['whatsapp', 'sms'])->default('whatsapp');
            $table->enum('tipo', [
                'prazo_fatal', 'prazo_vencendo', 'prazo_vencido',
                'cobranca', 'audiencia', 'andamento_cliente', 'teste'
            ]);
            $table->string('destinatario_nome');
            $table->string('destinatario_telefone', 30);
            $table->text('mensagem');
            $table->enum('status', ['enviado', 'falha'])->default('enviado');
            $table->string('twilio_sid', 60)->nullable();
            $table->text('erro')->nullable();
            $table->string('referencia_tipo', 50)->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->timestamps();

            $table->index(['referencia_tipo', 'referencia_id', 'tipo']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes_whatsapp');
    }
};
