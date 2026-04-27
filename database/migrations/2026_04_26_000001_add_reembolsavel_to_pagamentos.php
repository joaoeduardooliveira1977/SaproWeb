<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->boolean('reembolsavel')->default(false)->after('pago');
            $table->boolean('reembolso_gerado')->default(false)->after('reembolsavel');
            $table->unsignedBigInteger('recebimento_reembolso_id')->nullable()->after('reembolso_gerado');
        });
    }

    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropColumn(['reembolsavel', 'reembolso_gerado', 'recebimento_reembolso_id']);
        });
    }
};
