<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // fornecedores — drop unique cnpj_cpf (cada tenant pode ter o mesmo fornecedor)
        if (Schema::hasTable('fornecedores') && !Schema::hasColumn('fornecedores', 'tenant_id')) {
            Schema::table('fornecedores', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->index('tenant_id');
                $table->dropUnique(['cnpj_cpf']);
            });
        }

        // origens_recebimento — drop unique descricao (cada tenant tem suas próprias origens)
        if (Schema::hasTable('origens_recebimento') && !Schema::hasColumn('origens_recebimento', 'tenant_id')) {
            Schema::table('origens_recebimento', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->index('tenant_id');
                $table->dropUnique(['descricao']);
            });
        }

        // whatsapp_templates — sem unique a remover
        if (Schema::hasTable('whatsapp_templates') && !Schema::hasColumn('whatsapp_templates', 'tenant_id')) {
            Schema::table('whatsapp_templates', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->index('tenant_id');
            });
        }

        // graus_risco — drop unique codigo
        if (Schema::hasTable('graus_risco') && !Schema::hasColumn('graus_risco', 'tenant_id')) {
            Schema::table('graus_risco', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->index('tenant_id');
                $table->dropUnique(['codigo']);
            });
        }

        // tipos_acao — drop unique codigo
        if (Schema::hasTable('tipos_acao') && !Schema::hasColumn('tipos_acao', 'tenant_id')) {
            Schema::table('tipos_acao', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->index('tenant_id');
                $table->dropUnique(['codigo']);
            });
        }

        // tipos_processo — drop unique codigo
        if (Schema::hasTable('tipos_processo') && !Schema::hasColumn('tipos_processo', 'tenant_id')) {
            Schema::table('tipos_processo', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->index('tenant_id');
                $table->dropUnique(['codigo']);
            });
        }
    }

    public function down(): void
    {
        $tabelas = [
            'fornecedores',
            'origens_recebimento',
            'whatsapp_templates',
            'graus_risco',
            'tipos_acao',
            'tipos_processo',
        ];

        foreach ($tabelas as $tabela) {
            if (Schema::hasTable($tabela) && Schema::hasColumn($tabela, 'tenant_id')) {
                Schema::table($tabela, function (Blueprint $table) {
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
