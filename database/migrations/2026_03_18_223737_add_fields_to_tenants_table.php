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
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'nome')) {
                $table->string('nome')->nullable();
            }
            if (!Schema::hasColumn('tenants', 'email')) {
                $table->string('email')->unique()->nullable();
            }
            if (!Schema::hasColumn('tenants', 'plano')) {
                $table->string('plano')->default('basico');
            }
            if (!Schema::hasColumn('tenants', 'ativo')) {
                $table->boolean('ativo')->default(true);
            }
    	});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            //
        });
    }
};
