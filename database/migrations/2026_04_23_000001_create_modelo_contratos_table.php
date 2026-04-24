<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modelo_contratos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('nome', 150);
            $table->string('tipo', 40)->default('honorario_processo'); // honorario_processo | consultivo | avulso
            $table->longText('texto');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->index('tenant_id');
        });

        Schema::table('contratos', function (Blueprint $table) {
            if (! Schema::hasColumn('contratos', 'modelo_id')) {
                $table->unsignedBigInteger('modelo_id')->nullable()->after('processo_id');
            }
            if (! Schema::hasColumn('contratos', 'texto_contrato')) {
                $table->longText('texto_contrato')->nullable()->after('modelo_id');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modelo_contratos');
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn(['modelo_id', 'texto_contrato']);
        });
    }
};
