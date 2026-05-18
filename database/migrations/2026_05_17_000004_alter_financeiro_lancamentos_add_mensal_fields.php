<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financeiro_lancamentos', function (Blueprint $table) {
            $table->unsignedBigInteger('contrato_mensal_id')->nullable()->after('contrato_servico_id');
            $table->unsignedBigInteger('mensalidade_id')->nullable()->after('contrato_mensal_id');
            $table->unsignedBigInteger('receita_processo_id')->nullable()->after('mensalidade_id');
            $table->string('origem_tipo', 20)->nullable()->after('receita_processo_id');
            // valores: 'mensal', 'processo', 'pontual', 'manual'
            $table->decimal('juros', 10, 2)->nullable()->default(0)->after('observacoes');
            $table->decimal('multa', 10, 2)->nullable()->default(0)->after('juros');
            $table->decimal('desconto', 10, 2)->nullable()->default(0)->after('multa');
            $table->decimal('valor_liquido', 10, 2)->nullable()->after('desconto');
        });
    }

    public function down(): void
    {
        Schema::table('financeiro_lancamentos', function (Blueprint $table) {
            $table->dropColumn([
                'contrato_mensal_id', 'mensalidade_id', 'receita_processo_id',
                'origem_tipo', 'juros', 'multa', 'desconto', 'valor_liquido',
            ]);
        });
    }
};
