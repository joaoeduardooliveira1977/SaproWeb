<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'limite_storage_mb')) {
                $table->integer('limite_storage_mb')->default(500)->after('limite_usuarios');
            }
            if (!Schema::hasColumn('tenants', 'plano_expira_em')) {
                $table->timestamp('plano_expira_em')->nullable()->after('trial_expira_em');
            }
            // Garante que colunas que podem não existir sejam criadas
            if (!Schema::hasColumn('tenants', 'limite_processos')) {
                $table->integer('limite_processos')->default(100)->after('onboarding_concluido');
            }
            if (!Schema::hasColumn('tenants', 'limite_usuarios')) {
                $table->integer('limite_usuarios')->default(5)->after('limite_processos');
            }
            if (!Schema::hasColumn('tenants', 'trial_expira_em')) {
                $table->timestamp('trial_expira_em')->nullable()->after('limite_usuarios');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['limite_storage_mb', 'plano_expira_em']);
        });
    }
};
