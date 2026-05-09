<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['assuntos', 'reparticoes', 'secretarias', 'varas'] as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('updated_at');
            });
        }
    }

    public function down(): void
    {
        foreach (['assuntos', 'reparticoes', 'secretarias', 'varas'] as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }
};