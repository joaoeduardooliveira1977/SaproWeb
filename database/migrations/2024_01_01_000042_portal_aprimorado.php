<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Campo para liberar documento no portal do cliente
        Schema::table('documentos', function (Blueprint $table) {
            $table->boolean('portal_visivel')->default(false)->after('data_documento');
        });

        // Mensagens entre cliente e escritório
        Schema::create('portal_mensagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->constrained('pessoas')->cascadeOnDelete();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->text('mensagem');
            $table->string('de', 20)->default('cliente'); // cliente | escritorio
            $table->boolean('lida_cliente')->default(false);
            $table->boolean('lida_escritorio')->default(false);
            $table->timestamps();

            $table->index(['pessoa_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn('portal_visivel');
        });
        Schema::dropIfExists('portal_mensagens');
    }
};
