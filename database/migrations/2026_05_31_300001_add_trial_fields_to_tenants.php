<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'trial_iniciado_em')) {
                $table->timestamp('trial_iniciado_em')->nullable()->after('trial_expira_em');
            }
            if (!Schema::hasColumn('tenants', 'origem')) {
                $table->string('origem', 50)->nullable()->after('trial_iniciado_em');
            }
            if (!Schema::hasColumn('tenants', 'responsavel_nome')) {
                $table->string('responsavel_nome', 150)->nullable()->after('origem');
            }
            if (!Schema::hasColumn('tenants', 'responsavel_telefone')) {
                $table->string('responsavel_telefone', 30)->nullable()->after('responsavel_nome');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['trial_iniciado_em', 'origem', 'responsavel_nome', 'responsavel_telefone']);
        });
    }
};
