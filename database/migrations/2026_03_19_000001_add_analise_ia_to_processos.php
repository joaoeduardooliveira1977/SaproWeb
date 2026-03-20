<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->text('analise_ia')->nullable()->after('observacoes');
            $table->timestamp('analise_ia_em')->nullable()->after('analise_ia');
        });
    }

    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn(['analise_ia', 'analise_ia_em']);
        });
    }
};
