<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->string('categoria', 60)->nullable()->after('descricao');
        });

        // Make processo_id nullable (was NOT NULL with cascadeOnDelete)
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropForeign(['processo_id']);
        });

        DB::statement('ALTER TABLE pagamentos ALTER COLUMN processo_id DROP NOT NULL');

        Schema::table('pagamentos', function (Blueprint $table) {
            $table->foreign('processo_id')->references('id')->on('processos')->nullOnDelete();
        });

        // Backfill tenant_id from processo
        DB::statement('UPDATE pagamentos SET tenant_id = processos.tenant_id FROM processos WHERE pagamentos.processo_id = processos.id');
    }

    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'categoria']);
            $table->dropForeign(['processo_id']);
        });

        DB::statement('ALTER TABLE pagamentos ALTER COLUMN processo_id SET NOT NULL');

        Schema::table('pagamentos', function (Blueprint $table) {
            $table->foreign('processo_id')->references('id')->on('processos')->cascadeOnDelete();
        });
    }
};
