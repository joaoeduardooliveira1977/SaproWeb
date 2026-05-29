<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'dominio')) {
                $table->string('dominio', 253)->nullable()->unique()->after('slug');
            }
            if (!Schema::hasColumn('tenants', 'cor_primaria')) {
                $table->string('cor_primaria', 20)->default('#1a3a5c')->after('logo');
            }
            if (!Schema::hasColumn('tenants', 'cor_secundaria')) {
                $table->string('cor_secundaria', 20)->default('#c9a84c')->after('cor_primaria');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('tenants', 'dominio')       ? 'dominio'       : null,
                Schema::hasColumn('tenants', 'cor_primaria')  ? 'cor_primaria'  : null,
                Schema::hasColumn('tenants', 'cor_secundaria')? 'cor_secundaria': null,
            ]));
        });
    }
};
