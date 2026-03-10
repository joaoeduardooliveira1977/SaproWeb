<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Pessoas ────────────────────────────────────
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('cpf_cnpj', 18)->nullable()->unique();
            $table->string('rg', 20)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('logradouro', 200)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->char('estado', 2)->nullable();
            $table->char('cep', 9)->nullable();
            $table->string('oab', 30)->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // ── Tipos de Pessoa (multi-tipo sem duplicidade) 
        Schema::create('pessoa_tipos', function (Blueprint $table) {
            $table->foreignId('pessoa_id')->constrained('pessoas')->cascadeOnDelete();
            $table->string('tipo', 30); // Cliente, Advogado, Juiz, Parte Contrária, Usuário
            $table->primary(['pessoa_id', 'tipo']);
        });

        // ── Usuários do Sistema ────────────────────────
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->nullable()->constrained('pessoas')->nullOnDelete();
            $table->string('login', 60)->unique();
            $table->string('password'); // senha_hash — Laravel usa 'password' por convenção
            $table->enum('perfil', ['admin', 'advogado', 'operador', 'visualizador'])->default('operador');
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultimo_acesso')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('pessoa_tipos');
        Schema::dropIfExists('pessoas');
    }
};
