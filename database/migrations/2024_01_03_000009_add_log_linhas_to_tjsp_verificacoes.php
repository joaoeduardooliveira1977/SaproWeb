<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tjsp_verificacoes') || Schema::hasColumn('tjsp_verificacoes', 'log_linhas')) {
            return;
        }

        Schema::table('tjsp_verificacoes', function (Blueprint $table) {
            $table->jsonb('log_linhas')->nullable()->after('novos_andamentos');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tjsp_verificacoes') || !Schema::hasColumn('tjsp_verificacoes', 'log_linhas')) {
            return;
        }

        Schema::table('tjsp_verificacoes', function (Blueprint $table) {
            $table->dropColumn('log_linhas');
        });
    }
};
