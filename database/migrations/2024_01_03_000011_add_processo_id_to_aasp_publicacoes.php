<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('aasp_publicacoes') || Schema::hasColumn('aasp_publicacoes', 'processo_id')) {
            return;
        }

        Schema::table('aasp_publicacoes', function (Blueprint $table) {
            $table->foreignId('processo_id')->nullable()->after('codigo_aasp')
                  ->constrained('processos')->nullOnDelete();
            $table->index('processo_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('aasp_publicacoes') || !Schema::hasColumn('aasp_publicacoes', 'processo_id')) {
            return;
        }

        Schema::table('aasp_publicacoes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('processo_id');
        });
    }
};
