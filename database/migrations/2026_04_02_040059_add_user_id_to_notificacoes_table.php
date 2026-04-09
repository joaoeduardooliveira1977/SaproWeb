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
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        }
        if (!Schema::hasColumn('notificacoes', 'tipo')) {
            $table->string('tipo')->default('informativo');
        }
        if (!Schema::hasColumn('notificacoes', 'titulo')) {
            $table->string('titulo')->nullable();
        }
        if (!Schema::hasColumn('notificacoes', 'lida')) {
            $table->boolean('lida')->default(false);
        }
        if (!Schema::hasColumn('notificacoes', 'processo_id')) {
            $table->foreignId('processo_id')->nullable()->constrained()->nullOnDelete();
        }
    });
}

public function down(): void
{
    Schema::table('notificacoes', function (Blueprint $table) {
        $table->dropColumnIfExists('user_id');
        $table->dropColumnIfExists('tipo');
        $table->dropColumnIfExists('titulo');
        $table->dropColumnIfExists('lida');
        $table->dropColumnIfExists('processo_id');
    });
}

};