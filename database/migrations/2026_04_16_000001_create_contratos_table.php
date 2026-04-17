<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('cliente_id')->constrained('pessoas')->cascadeOnDelete();

            $table->enum('tipo', [
                'honorario_processo',   // judicial ou extrajudicial
                'consultivo',           // assessoria mensal
                'avulso',               // serviço pontual
            ])->default('honorario_processo');

            $table->string('descricao', 300);
            $table->text('observacoes')->nullable();

            $table->enum('forma_cobranca', [
                'parcelado',
                'mensal_recorrente',
                'exito',
                'avulso',
            ])->default('parcelado');

            $table->decimal('valor', 14, 2)->default(0);
            $table->decimal('percentual_exito', 5, 2)->nullable();
            $table->unsignedTinyInteger('dia_vencimento')->nullable(); // 1-28 para recorrente

            $table->date('data_inicio');
            $table->date('data_fim')->nullable();

            $table->enum('status', ['rascunho', 'ativo', 'suspenso', 'encerrado'])->default('ativo');

            // Contrato assinado (anexo)
            $table->string('arquivo', 500)->nullable();
            $table->string('arquivo_original', 300)->nullable();

            // Validação financeiro/admin
            $table->boolean('validado')->default(false);
            $table->timestamp('validado_em')->nullable();
            $table->string('validado_por', 100)->nullable();

            $table->timestamps();
        });

        Schema::create('contrato_servicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();

            $table->string('descricao', 300);
            $table->enum('tipo', [
                'honorario',
                'consultoria',
                'exito',
                'avulso',
                'repasse',
                'outro',
            ])->default('honorario');

            $table->decimal('valor', 14, 2)->default(0);
            $table->decimal('percentual', 5, 2)->nullable();

            $table->enum('status', ['ativo', 'encerrado'])->default('ativo');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('contrato_repasses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
            $table->foreignId('indicador_id')->constrained('pessoas')->cascadeOnDelete();

            $table->enum('tipo_calculo', ['percentual', 'fixo'])->default('percentual');
            $table->decimal('percentual', 5, 2)->nullable();
            $table->decimal('valor_fixo', 14, 2)->nullable();

            $table->enum('frequencia', ['mensal', 'unico'])->default('mensal');
            $table->enum('status', ['ativo', 'encerrado'])->default('ativo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_repasses');
        Schema::dropIfExists('contrato_servicos');
        Schema::dropIfExists('contratos');
    }
};
