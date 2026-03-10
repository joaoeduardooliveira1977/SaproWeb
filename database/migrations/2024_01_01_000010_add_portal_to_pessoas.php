<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->string('portal_senha')->nullable()->after('ativo');
            $table->boolean('portal_ativo')->default(false)->after('portal_senha');
            $table->timestamp('portal_ultimo_acesso')->nullable()->after('portal_ativo');
        });
    }

    public function down(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropColumn(['portal_senha', 'portal_ativo', 'portal_ultimo_acesso']);
        });
    }
};
