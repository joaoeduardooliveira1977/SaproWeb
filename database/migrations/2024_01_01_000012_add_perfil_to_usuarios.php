<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Troca o enum perfil: remove a versão antiga e recria com os novos valores
        // (SQLite não suporta ALTER COLUMN, então dropar e recriar é o caminho correto)
        if (Schema::hasColumn('usuarios', 'perfil')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('perfil');
            });
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $table->enum('perfil', ['admin','advogado','estagiario','financeiro','recepcionista'])
                  ->default('estagiario');
        });

        // Adiciona nome (para exibição direta sem precisar de JOIN com pessoas)
        if (!Schema::hasColumn('usuarios', 'nome')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('nome', 150)->nullable();
            });
        }

        // Adiciona email
        if (!Schema::hasColumn('usuarios', 'email')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('email', 150)->nullable();
            });
        }

        // Adiciona telefone
        if (!Schema::hasColumn('usuarios', 'telefone')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('telefone', 20)->nullable();
            });
        }

        // Garante que o usuário admin tenha perfil admin
        DB::statement("UPDATE usuarios SET perfil = 'admin' WHERE login = 'admin'");
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(array_filter(
                ['nome', 'email', 'telefone'],
                fn($col) => Schema::hasColumn('usuarios', $col)
            ));
        });

        if (Schema::hasColumn('usuarios', 'perfil')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('perfil');
            });
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $table->enum('perfil', ['admin', 'advogado', 'operador', 'visualizador'])->default('operador');
        });
    }
};
