<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'master_2fa_secret')) {
                $table->text('master_2fa_secret')->nullable()->after('perfil');
            }
            if (!Schema::hasColumn('usuarios', 'master_2fa_ativo')) {
                $table->boolean('master_2fa_ativo')->default(false)->after('master_2fa_secret');
            }
            if (!Schema::hasColumn('usuarios', 'master_2fa_confirmado_em')) {
                $table->timestamp('master_2fa_confirmado_em')->nullable()->after('master_2fa_ativo');
            }
            if (!Schema::hasColumn('usuarios', 'master_2fa_recovery_codes')) {
                $table->json('master_2fa_recovery_codes')->nullable()->after('master_2fa_confirmado_em');
            }
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn([
                'master_2fa_secret',
                'master_2fa_ativo',
                'master_2fa_confirmado_em',
                'master_2fa_recovery_codes',
            ]);
        });
    }
};
