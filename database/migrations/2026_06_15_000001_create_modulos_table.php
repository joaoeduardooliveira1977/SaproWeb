<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->string('chave', 50)->unique();
            $table->string('nome', 100);
            $table->string('descricao', 255)->nullable();
            $table->string('icone', 50)->nullable();
            $table->boolean('ativo')->default(true);
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();
        });

        Schema::create('tenant_modulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('modulo_id')->constrained('modulos')->cascadeOnDelete();
            $table->boolean('ativo')->default(true);
            $table->foreignId('ativado_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('ativado_em')->nullable();
            $table->string('observacao', 255)->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'modulo_id']);
        });

        Schema::create('plano_modulos', function (Blueprint $table) {
            $table->id();
            $table->string('plano', 20);
            $table->foreignId('modulo_id')->constrained('modulos')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['plano', 'modulo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plano_modulos');
        Schema::dropIfExists('tenant_modulos');
        Schema::dropIfExists('modulos');
    }
};
