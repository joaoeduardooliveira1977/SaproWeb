<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('andamentos', function (Blueprint $table) {
            $table->boolean('interno')->default(false)->after('descricao');
        });
    }

    public function down(): void
    {
        Schema::table('andamentos', function (Blueprint $table) {
            $table->dropColumn('interno');
        });
    }
};
