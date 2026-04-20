<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->foreignId('advogado_responsavel_id')
                ->nullable()
                ->after('cliente_id')
                ->constrained('pessoas')
                ->nullOnDelete();

            $table->foreignId('processo_id')
                ->nullable()
                ->after('descricao')
                ->constrained('processos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('processo_id');
            $table->dropConstrainedForeignId('advogado_responsavel_id');
        });
    }
};
