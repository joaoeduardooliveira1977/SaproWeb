<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            if (!Schema::hasColumn('pessoas', 'filial_id')) {
                $table->unsignedBigInteger('filial_id')->nullable()->after('tenant_id');
                $table->foreign('filial_id')->references('id')->on('filiais')->onDelete('set null');
            }
            if (!Schema::hasColumn('pessoas', 'modelo_relatorio')) {
                $table->string('modelo_relatorio', 30)->nullable()->after('filial_id');
            }
            if (!Schema::hasColumn('pessoas', 'referencia')) {
                $table->string('referencia', 150)->nullable()->after('modelo_relatorio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropForeignIfExists(['filial_id']);
            $table->dropColumnIfExists(['filial_id', 'modelo_relatorio', 'referencia']);
        });
    }
};
