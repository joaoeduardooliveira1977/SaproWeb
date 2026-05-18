<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos_mensais', function (Blueprint $table) {
            $table->string('responsavel')->nullable()->after('descricao');
        });
    }

    public function down(): void
    {
        Schema::table('contratos_mensais', function (Blueprint $table) {
            $table->dropColumn('responsavel');
        });
    }
};
