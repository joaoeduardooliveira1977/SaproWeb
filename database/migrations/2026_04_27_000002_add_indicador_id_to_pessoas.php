<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->foreignId('indicador_id')
                  ->nullable()
                  ->after('administradora_id')
                  ->constrained('indicadores')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Indicador::class);
            $table->dropColumn('indicador_id');
        });
    }
};
