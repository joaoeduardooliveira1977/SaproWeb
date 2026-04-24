<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrato_servicos', function (Blueprint $table) {
            $table->decimal('valor_realizado', 14, 2)->nullable()->after('numero_parcelas');
            $table->timestamp('realizado_em')->nullable()->after('valor_realizado');
            $table->string('realizado_por', 100)->nullable()->after('realizado_em');
        });
    }

    public function down(): void
    {
        Schema::table('contrato_servicos', function (Blueprint $table) {
            $table->dropColumn(['valor_realizado', 'realizado_em', 'realizado_por']);
        });
    }
};
