<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Remover a constraint unique global em numero (se existir)
        DB::statement('ALTER TABLE processos DROP CONSTRAINT IF EXISTS processos_numero_unique');

        // 2. Tornar numero nullable (processos sem número não devem quebrar)
        Schema::table('processos', function (Blueprint $table) {
            $table->string('numero', 30)->nullable()->change();
        });

        // 3. Converter strings vazias para NULL
        DB::statement("UPDATE processos SET numero = NULL WHERE numero = ''");

        // 4. Índice único parcial por (numero, tenant_id) — ignora NULLs automaticamente
        DB::statement('
            CREATE UNIQUE INDEX IF NOT EXISTS processos_numero_tenant_unique
            ON processos (numero, tenant_id)
            WHERE numero IS NOT NULL
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS processos_numero_tenant_unique');
        Schema::table('processos', function (Blueprint $table) {
            $table->string('numero', 30)->nullable(false)->change();
        });
        DB::statement('ALTER TABLE processos ADD CONSTRAINT processos_numero_unique UNIQUE (numero)');
    }
};
