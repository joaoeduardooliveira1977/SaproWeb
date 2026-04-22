<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfil_permissoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('perfil', 30);
            $table->string('modulo', 50);
            $table->boolean('permitido')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'perfil', 'modulo']);
            $table->index(['tenant_id', 'perfil']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfil_permissoes');
    }
};
