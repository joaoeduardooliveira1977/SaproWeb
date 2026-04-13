<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notificacoes')) {
            return;
        }

        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['critico', 'prazo', 'andamento', 'decisao', 'informativo'])->default('informativo');
            $table->string('titulo');
            $table->text('mensagem');
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->foreignId('user_id')->constrained('usuarios')->cascadeOnDelete();
            $table->boolean('lida')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'lida']);
            $table->index(['processo_id']);
        });
    }

    public function down(): void
    {
        // A tabela pode ter sido criada por uma migration anterior.
    }
};
