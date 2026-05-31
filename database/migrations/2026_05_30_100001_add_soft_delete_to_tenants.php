<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->softDeletes()->after('onboarding_concluido');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
            $table->string('delete_reason')->nullable()->after('deleted_by');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'deleted_by', 'delete_reason']);
        });
    }
};
