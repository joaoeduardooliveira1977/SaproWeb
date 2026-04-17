<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financeiro_lancamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            // Origens (todas opcionais — lançamento pode ser direto ao cliente)
            $table->foreignId('cliente_id')->constrained('pessoas')->cascadeOnDelete();
            $table->foreignId('contrato_id')->nullable()->constrained('contratos')->nullOnDelete();
            $table->foreignId('contrato_servico_id')->nullable()->constrained('contrato_servicos')->nullOnDelete();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();

            $table->enum('tipo', ['receita', 'despesa', 'repasse'])->default('receita');

            $table->string('descricao', 300);
            $table->decimal('valor', 14, 2);
            $table->date('vencimento');
            $table->date('data_pagamento')->nullable();
            $table->decimal('valor_pago', 14, 2)->nullable();

            $table->enum('status', ['previsto', 'recebido', 'atrasado', 'cancelado'])->default('previsto');

            $table->string('forma_pagamento', 50)->nullable(); // pix, boleto, ted, cheque
            $table->text('observacoes')->nullable();

            // Número da parcela (para contratos parcelados/mensais)
            $table->unsignedSmallInteger('numero_parcela')->nullable();
            $table->unsignedSmallInteger('total_parcelas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financeiro_lancamentos');
    }
};
