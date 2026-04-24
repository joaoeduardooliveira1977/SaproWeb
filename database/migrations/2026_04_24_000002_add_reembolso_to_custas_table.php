<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custas', function (Blueprint $table) {
            $table->boolean('reembolsavel')->default(true)->after('pago');
            $table->foreignId('cobranca_lancamento_id')->nullable()->after('reembolsavel')
                ->constrained('financeiro_lancamentos')->nullOnDelete();
            $table->timestamp('cobrado_em')->nullable()->after('cobranca_lancamento_id');
            $table->string('cobrado_por', 100)->nullable()->after('cobrado_em');
        });
    }

    public function down(): void
    {
        Schema::table('custas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cobranca_lancamento_id');
            $table->dropColumn(['reembolsavel', 'cobrado_em', 'cobrado_por']);
        });
    }
};
