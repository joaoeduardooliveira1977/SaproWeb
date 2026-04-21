<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrato_servicos', function (Blueprint $table) {
            $table->date('vencimento')->nullable()->after('valor');
            $table->unsignedTinyInteger('numero_parcelas')->default(1)->after('vencimento');
        });
    }

    public function down(): void
    {
        Schema::table('contrato_servicos', function (Blueprint $table) {
            $table->dropColumn(['vencimento', 'numero_parcelas']);
        });
    }
};
