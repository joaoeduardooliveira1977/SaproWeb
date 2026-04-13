<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            if (!Schema::hasColumn('processos', 'score')) {
                $table->enum('score', ['critico', 'atencao', 'normal'])->default('normal')->after('status');
            }
            if (!Schema::hasColumn('processos', 'resumo_ia')) {
                $table->text('resumo_ia')->nullable()->after('score');
            }
            if (!Schema::hasColumn('processos', 'monitoramento_ativo')) {
                $table->boolean('monitoramento_ativo')->default(false)->after('resumo_ia');
            }
            if (!Schema::hasColumn('processos', 'frequencia_monitoramento')) {
                $table->enum('frequencia_monitoramento', ['6h', '12h', 'diario'])->default('diario')->after('monitoramento_ativo');
            }
            if (!Schema::hasColumn('processos', 'ultima_verificacao_datajud')) {
                $table->timestamp('ultima_verificacao_datajud')->nullable()->after('frequencia_monitoramento');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('processos')) {
            return;
        }

        $columns = array_filter(
            [
                'score',
                'resumo_ia',
                'monitoramento_ativo',
                'frequencia_monitoramento',
                'ultima_verificacao_datajud',
            ],
            fn ($column) => Schema::hasColumn('processos', $column)
        );

        if ($columns === []) {
            return;
        }

        Schema::table('processos', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }
};
