<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->enum('perfil', ['admin','advogado','estagiario','financeiro','recepcionista'])
                  ->default('estagiario')
                  ->after('nome');
            $table->boolean('ativo')->default(true)->after('perfil');
            $table->string('telefone', 20)->nullable()->after('ativo');
            $table->timestamp('ultimo_acesso')->nullable()->after('telefone');
        });

        // Admin já existente vira admin
        DB::statement("UPDATE usuarios SET perfil = 'admin' WHERE login = 'admin'");
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['perfil', 'ativo', 'telefone', 'ultimo_acesso']);
        });
    }
};
