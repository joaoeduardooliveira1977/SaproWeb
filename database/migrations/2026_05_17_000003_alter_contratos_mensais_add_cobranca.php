<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos_mensais', function (Blueprint $table) {
            $table->string('forma_cobranca', 30)->default('pix')->after('observacoes');
            $table->string('email_financeiro')->nullable()->after('forma_cobranca');
            $table->string('whatsapp_financeiro', 20)->nullable()->after('email_financeiro');
            $table->boolean('envio_automatico')->default(false)->after('whatsapp_financeiro');
            $table->unsignedBigInteger('created_by')->nullable()->after('envio_automatico');
        });
    }

    public function down(): void
    {
        Schema::table('contratos_mensais', function (Blueprint $table) {
            $table->dropColumn(['forma_cobranca', 'email_financeiro', 'whatsapp_financeiro', 'envio_automatico', 'created_by']);
        });
    }
};
