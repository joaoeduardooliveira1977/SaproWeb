<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->boolean('extrajudicial')->default(false)->after('data_distribuicao');
            $table->string('autor_reu', 10)->nullable()->after('parte_contraria');
            $table->string('unidade', 100)->nullable()->after('autor_reu');
        });
    }

    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn(['extrajudicial', 'autor_reu', 'unidade']);
        });
    }
};
