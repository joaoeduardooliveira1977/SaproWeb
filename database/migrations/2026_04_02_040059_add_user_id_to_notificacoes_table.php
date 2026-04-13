<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


public function up(): void
{
    Schema::table('notificacoes', function (Blueprint $table) {
        if (!Schema::hasColumn('notificacoes', 'user_id')) {
            $table->foreignId('user_id')->nullable()->constrained('usuarios')->nullOnDelete();
        }
        if (!Schema::hasColumn('notificacoes', 'usuario_id')) {
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
        }
        if (!Schema::hasColumn('notificacoes', 'tipo')) {
            $table->string('tipo')->default('informativo');
        }
        if (!Schema::hasColumn('notificacoes', 'titulo')) {
            $table->string('titulo')->nullable();
        }
        if (!Schema::hasColumn('notificacoes', 'mensagem')) {
            $table->text('mensagem')->nullable();
        }
        if (!Schema::hasColumn('notificacoes', 'lida')) {
            $table->boolean('lida')->default(false);
        }
        if (!Schema::hasColumn('notificacoes', 'processo_id')) {
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
        }
        if (!Schema::hasColumn('notificacoes', 'referencia_tipo')) {
            $table->string('referencia_tipo', 50)->nullable();
        }
        if (!Schema::hasColumn('notificacoes', 'referencia_id')) {
            $table->unsignedBigInteger('referencia_id')->nullable();
        }
        if (!Schema::hasColumn('notificacoes', 'link')) {
            $table->string('link', 200)->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('notificacoes', function (Blueprint $table) {
        if (Schema::hasColumn('notificacoes', 'user_id')) {
            $table->dropConstrainedForeignId('user_id');
        }
        if (Schema::hasColumn('notificacoes', 'processo_id')) {
            $table->dropConstrainedForeignId('processo_id');
        }
    });
}

};
