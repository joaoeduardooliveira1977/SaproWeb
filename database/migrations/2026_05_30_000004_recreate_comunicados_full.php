<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove a tabela simples criada anteriormente
        Schema::dropIfExists('comunicados');

        Schema::create('comunicados', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('mensagem');
            $table->enum('tipo', [
                'manutencao_emergencial',
                'manutencao_programada',
                'sistema_restaurado',
                'atualizacao',
                'trial_expirando',
                'pagamento_pendente',
                'nova_funcionalidade',
                'informativo',
            ])->default('informativo');
            $table->enum('prioridade', ['banner', 'modal', 'notificacao'])->default('banner');
            $table->enum('destino', ['todos', 'tenant_especifico', 'plano_especifico'])->default('todos');
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('plano', 50)->nullable(); // quando destino = plano_especifico
            $table->timestamp('data_inicio')->useCurrent();
            $table->timestamp('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->unsignedBigInteger('criado_por')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicados');

        // Recria a versão original simples (rollback seguro)
        Schema::create('comunicados', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('mensagem');
            $table->enum('tipo', ['info', 'aviso', 'critico'])->default('info');
            $table->boolean('ativo')->default(true);
            $table->timestamp('expira_em')->nullable();
            $table->unsignedBigInteger('criado_por')->nullable();
            $table->timestamps();
        });
    }
};
