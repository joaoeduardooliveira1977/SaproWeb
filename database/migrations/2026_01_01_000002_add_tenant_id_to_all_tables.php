<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tabelas = [
            'pessoas', 'prazos', 'agenda', 'documentos',
            'andamentos', 'recebimentos', 'honorarios',
            'audiencias', 'minutas', 'procuracoes',
        ];

        foreach ($tabelas as $tabela) {
            if (Schema::hasTable($tabela) && !Schema::hasColumn($tabela, 'tenant_id')) {
                Schema::table($tabela, function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                    $table->index('tenant_id');
                });
            }
        }
    }

    public function down(): void
    {
        $tabelas = [
            'pessoas', 'prazos', 'agenda', 'documentos',
            'andamentos', 'recebimentos', 'honorarios',
            'audiencias', 'minutas', 'procuracoes',
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
