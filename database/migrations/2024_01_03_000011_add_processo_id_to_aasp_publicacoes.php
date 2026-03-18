<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aasp_publicacoes', function (Blueprint $table) {
            $table->foreignId('processo_id')->nullable()->after('codigo_aasp')
                  ->constrained('processos')->nullOnDelete();
            $table->index('processo_id');
        });
    }

    public function down(): void
    {
        Schema::table('aasp_publicacoes', function (Blueprint $table) {
            $table->dropForeign(['processo_id']);
            $table->dropColumn('processo_id');
        });
    }
};
