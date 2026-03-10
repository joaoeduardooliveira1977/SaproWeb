<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tjsp_verificacoes', function (Blueprint $table) {
            $table->json('filtros')->nullable()->after('novos_andamentos');
        });
    }

    public function down(): void
    {
        Schema::table('tjsp_verificacoes', function (Blueprint $table) {
            $table->dropColumn('filtros');
        });
    }
};
