<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('processos')) {
            return;
        }

        Schema::table('processos', function (Blueprint $table) {
            if (!Schema::hasColumn('processos', 'analise_ia')) {
                $table->text('analise_ia')->nullable()->after('observacoes');
            }
            if (!Schema::hasColumn('processos', 'analise_ia_em')) {
                $table->timestamp('analise_ia_em')->nullable()->after('analise_ia');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('processos')) {
            return;
        }

        $columns = array_filter(
            ['analise_ia', 'analise_ia_em'],
            fn ($column) => Schema::hasColumn('processos', $column)
        );

        if ($columns === []) {
            return;
        }

        Schema::table('processos', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }
};
