<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('documentos') || Schema::hasColumn('documentos', 'andamento_id')) {
            return;
        }

        Schema::table('documentos', function (Blueprint $table) {
            $table->foreignId('andamento_id')
                ->nullable()
                ->after('processo_id')
                ->constrained('andamentos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('documentos') || !Schema::hasColumn('documentos', 'andamento_id')) {
            return;
        }

        Schema::table('documentos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('andamento_id');
        });
    }
};
