<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_admin_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('master_admin_logs', 'user_agent')) {
                $table->string('user_agent', 300)->nullable()->after('ip');
            }
            if (!Schema::hasColumn('master_admin_logs', 'contexto')) {
                $table->string('contexto', 50)->nullable()->after('acao'); // login_ok, login_fail, etc.
            }
        });

        // Índices para consultas frequentes
        try {
            Schema::table('master_admin_logs', function (Blueprint $table) {
                $table->index('admin_id');
                $table->index('tenant_id');
                $table->index('acao');
                $table->index('created_at');
            });
        } catch (\Exception) {
            // Ignora se já existem
        }
    }

    public function down(): void
    {
        Schema::table('master_admin_logs', function (Blueprint $table) {
            $table->dropColumn(['user_agent', 'contexto']);
        });
    }
};
