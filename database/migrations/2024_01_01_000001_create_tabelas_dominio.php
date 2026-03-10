<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Fases ──────────────────────────────────────
        Schema::create('fases', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('descricao', 100);
            $table->smallInteger('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // ── Graus de Risco ─────────────────────────────
        Schema::create('graus_risco', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('descricao', 50);
            $table->char('cor_hex', 7)->default('#64748b');
            $table->timestamps();
        });

        // ── Tipos de Ação ──────────────────────────────
        Schema::create('tipos_acao', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('descricao', 100);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // ── Tipos de Processo ──────────────────────────
        Schema::create('tipos_processo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('descricao', 100);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // ── Assuntos ───────────────────────────────────
        Schema::create('assuntos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('descricao', 100);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // ── Repartições ────────────────────────────────
        Schema::create('reparticoes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('descricao', 100);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // ── Secretarias ────────────────────────────────
        Schema::create('secretarias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('descricao', 100);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // ── Índices Monetários ─────────────────────────
        Schema::create('indices_monetarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('sigla', 10);
            $table->date('mes_ref');
            $table->decimal('percentual', 10, 6);
            $table->timestamps();
            $table->unique(['sigla', 'mes_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indices_monetarios');
        Schema::dropIfExists('secretarias');
        Schema::dropIfExists('reparticoes');
        Schema::dropIfExists('assuntos');
        Schema::dropIfExists('tipos_processo');
        Schema::dropIfExists('tipos_acao');
        Schema::dropIfExists('graus_risco');
        Schema::dropIfExists('fases');
    }
};
