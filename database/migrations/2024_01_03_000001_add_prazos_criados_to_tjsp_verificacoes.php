<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tjsp_verificacoes') || Schema::hasColumn('tjsp_verificacoes', 'prazos_criados')) {
            return;
        }

        Schema::table('tjsp_verificacoes', function (Blueprint $table) {
            $table->integer('prazos_criados')->default(0)->after('novos_total');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tjsp_verificacoes') || !Schema::hasColumn('tjsp_verificacoes', 'prazos_criados')) {
            return;
        }

        Schema::table('tjsp_verificacoes', function (Blueprint $table) {
            $table->dropColumn('prazos_criados');
        });
    }
};
