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
    if (!Schema::hasTable('processos') || Schema::hasColumn('processos', 'parte_contraria_id')) {
        return;
    }

    Schema::table('processos', function (Blueprint $table) {
        $table->foreignId('parte_contraria_id')->nullable()->constrained('pessoas')->nullOnDelete();
    });
}

public function down(): void
{
    if (!Schema::hasTable('processos') || !Schema::hasColumn('processos', 'parte_contraria_id')) {
        return;
    }

    Schema::table('processos', function (Blueprint $table) {
        $table->dropConstrainedForeignId('parte_contraria_id');
    });
}





};
