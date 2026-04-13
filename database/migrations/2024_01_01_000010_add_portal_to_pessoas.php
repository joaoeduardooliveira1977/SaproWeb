<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pessoas')) {
            return;
        }

        Schema::table('pessoas', function (Blueprint $table) {
            if (!Schema::hasColumn('pessoas', 'portal_senha')) {
                $table->string('portal_senha')->nullable()->after('ativo');
            }
            if (!Schema::hasColumn('pessoas', 'portal_ativo')) {
                $table->boolean('portal_ativo')->default(false)->after('portal_senha');
            }
            if (!Schema::hasColumn('pessoas', 'portal_ultimo_acesso')) {
                $table->timestamp('portal_ultimo_acesso')->nullable()->after('portal_ativo');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('pessoas')) {
            return;
        }

        $columns = array_filter(
            ['portal_senha', 'portal_ativo', 'portal_ultimo_acesso'],
            fn ($column) => Schema::hasColumn('pessoas', $column)
        );

        if ($columns === []) {
            return;
        }

        Schema::table('pessoas', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }
};
