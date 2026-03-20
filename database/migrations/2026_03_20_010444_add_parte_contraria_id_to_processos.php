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
    Schema::table('processos', function (Blueprint $table) {
        $table->unsignedBigInteger('parte_contraria_id')->nullable();
        $table->foreign('parte_contraria_id')->references('id')->on('pessoas')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('processos', function (Blueprint $table) {
        $table->dropForeign(['parte_contraria_id']);
        $table->dropColumn('parte_contraria_id');
    });
}





};
