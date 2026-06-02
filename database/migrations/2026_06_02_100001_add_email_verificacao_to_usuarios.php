<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'email_verificado')) {
                $table->boolean('email_verificado')->default(false)->after('email');
            }
            if (!Schema::hasColumn('usuarios', 'email_token_verificacao')) {
                $table->string('email_token_verificacao', 255)->nullable()->after('email_verificado');
            }
            if (!Schema::hasColumn('usuarios', 'email_token_expira_em')) {
                $table->timestamp('email_token_expira_em')->nullable()->after('email_token_verificacao');
            }
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn([
                'email_verificado',
                'email_token_verificacao',
                'email_token_expira_em',
            ]);
        });
    }
};
