<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'onboarding_concluido')) {
                $table->boolean('onboarding_concluido')->default(false)->after('ativo');
            }
            if (! Schema::hasColumn('tenants', 'endereco')) {
                $table->string('endereco', 200)->nullable()->after('cnpj');
            }
            if (! Schema::hasColumn('tenants', 'cidade')) {
                $table->string('cidade', 100)->nullable()->after('endereco');
            }
            if (! Schema::hasColumn('tenants', 'oab')) {
                $table->string('oab', 30)->nullable()->after('cidade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['onboarding_concluido', 'endereco', 'cidade', 'oab']);
        });
    }
};
