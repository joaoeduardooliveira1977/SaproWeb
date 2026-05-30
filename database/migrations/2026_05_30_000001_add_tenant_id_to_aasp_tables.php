<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // aasp_advogados
        if (!Schema::hasColumn('aasp_advogados', 'tenant_id')) {
            Schema::table('aasp_advogados', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')
                      ->constrained('tenants')->nullOnDelete();
                $table->index('tenant_id');
            });
        }

        // Remove o UNIQUE simples de codigo_aasp e troca por único composto
        // (mesmo código pode existir em tenants diferentes)
        Schema::table('aasp_advogados', function (Blueprint $table) {
            $table->dropUnique(['codigo_aasp']);
            $table->unique(['tenant_id', 'codigo_aasp'], 'aasp_advogados_tenant_codigo_unique');
        });

        // aasp_config
        if (!Schema::hasColumn('aasp_config', 'tenant_id')) {
            Schema::table('aasp_config', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')
                      ->constrained('tenants')->nullOnDelete();
                $table->index('tenant_id');
            });
        }

        // aasp_publicacoes
        if (!Schema::hasColumn('aasp_publicacoes', 'tenant_id')) {
            Schema::table('aasp_publicacoes', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')
                      ->constrained('tenants')->nullOnDelete();
                $table->index('tenant_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('aasp_advogados', function (Blueprint $table) {
            $table->dropUnique('aasp_advogados_tenant_codigo_unique');
            $table->unique(['codigo_aasp']);
            $table->dropConstrainedForeignId('tenant_id');
        });

        foreach (['aasp_config', 'aasp_publicacoes'] as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->dropConstrainedForeignId('tenant_id');
            });
        }
    }
};
