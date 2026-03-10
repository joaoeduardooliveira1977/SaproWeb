<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Processos ──────────────────────────────────
        Schema::create('processos', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->unique();
            $table->date('data_distribuicao')->nullable();
            $table->foreignId('cliente_id')->constrained('pessoas');
            $table->string('parte_contraria', 150)->nullable();
            $table->foreignId('advogado_id')->nullable()->constrained('pessoas')->nullOnDelete();
            $table->foreignId('juiz_id')->nullable()->constrained('pessoas')->nullOnDelete();
            $table->foreignId('tipo_acao_id')->nullable()->constrained('tipos_acao')->nullOnDelete();
            $table->foreignId('tipo_processo_id')->nullable()->constrained('tipos_processo')->nullOnDelete();
            $table->foreignId('fase_id')->nullable()->constrained('fases')->nullOnDelete();
            $table->foreignId('assunto_id')->nullable()->constrained('assuntos')->nullOnDelete();
            $table->foreignId('risco_id')->nullable()->constrained('graus_risco')->nullOnDelete();
            $table->foreignId('secretaria_id')->nullable()->constrained('secretarias')->nullOnDelete();
            $table->foreignId('reparticao_id')->nullable()->constrained('reparticoes')->nullOnDelete();
            $table->string('vara', 150)->nullable();
            $table->decimal('valor_causa', 15, 2)->default(0);
            $table->decimal('valor_risco', 15, 2)->default(0);
            $table->text('observacoes')->nullable();
            $table->enum('status', ['Ativo', 'Arquivado', 'Encerrado', 'Suspenso'])->default('Ativo');
            $table->foreignId('criado_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
        });

        // ── Andamentos ─────────────────────────────────
        Schema::create('andamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->date('data')->default(DB::raw('CURRENT_DATE'));
            $table->text('descricao');
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
        });

        // ── Agenda ─────────────────────────────────────
        Schema::create('agenda', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->dateTime('data_hora');
            $table->string('local', 200)->nullable();
            $table->enum('tipo', ['Audiência', 'Prazo', 'Reunião', 'Consulta', 'Despacho', 'Outros'])->default('Outros');
            $table->boolean('urgente')->default(false);
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->foreignId('responsavel_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->boolean('concluido')->default(false);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        // ── Custas ─────────────────────────────────────
        Schema::create('custas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->date('data')->default(DB::raw('CURRENT_DATE'));
            $table->string('descricao', 200);
            $table->decimal('valor', 15, 2)->default(0);
            $table->boolean('pago')->default(false);
            $table->date('data_pagamento')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
        });

        // ── Auditoria ──────────────────────────────────
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('login', 60)->nullable();
            $table->string('acao', 100);
            $table->string('tabela', 60)->nullable();
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->json('dados_antes')->nullable();
            $table->json('dados_apos')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
        Schema::dropIfExists('custas');
        Schema::dropIfExists('agenda');
        Schema::dropIfExists('andamentos');
        Schema::dropIfExists('processos');
    }
};
