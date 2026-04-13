<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('processos') || Schema::hasColumn('processos', 'tjsp_ultima_consulta')) {
            return;
        }

        Schema::table('processos', function (Blueprint $table) {
            $table->timestamp('tjsp_ultima_consulta')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('processos') || !Schema::hasColumn('processos', 'tjsp_ultima_consulta')) {
            return;
        }

        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn('tjsp_ultima_consulta');
        });
    }
};
