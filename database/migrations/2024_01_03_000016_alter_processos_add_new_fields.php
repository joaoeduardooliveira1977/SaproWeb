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
            if (!Schema::hasColumn('processos', 'extrajudicial')) {
                $table->boolean('extrajudicial')->default(false)->after('data_distribuicao');
            }
            if (!Schema::hasColumn('processos', 'autor_reu')) {
                $table->string('autor_reu', 10)->nullable()->after('parte_contraria');
            }
            if (!Schema::hasColumn('processos', 'unidade')) {
                $table->string('unidade', 100)->nullable()->after('autor_reu');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('processos')) {
            return;
        }

        $columns = array_filter(
            ['extrajudicial', 'autor_reu', 'unidade'],
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
