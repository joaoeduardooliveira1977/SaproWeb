<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fases', function (Blueprint $table) {
            // Remove a constraint global única em codigo
            $table->dropUnique(['codigo']);

            // Adiciona constraint composta: codigo único por tenant
            // nulls são tratados como distintos no PostgreSQL, então
            // múltiplos registros com tenant_id NULL e mesmo codigo
            // não conflitam entre si — comportamento correto para dados globais.
            $table->unique(['codigo', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('fases', function (Blueprint $table) {
            $table->dropUnique(['codigo', 'tenant_id']);
            $table->unique(['codigo']);
        });
    }
};
