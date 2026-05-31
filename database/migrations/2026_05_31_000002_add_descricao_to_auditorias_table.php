<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('auditorias', 'descricao')) {
            Schema::table('auditorias', function (Blueprint $table) {
                $table->text('descricao')->nullable()->after('acao');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('auditorias', 'descricao')) {
            Schema::table('auditorias', function (Blueprint $table) {
                $table->dropColumn('descricao');
            });
        }
    }
};
