<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_jurisprudencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->bigInteger('tenant_id');
            $table->bigInteger('user_id')->nullable();
            $table->string('tribunal', 20)->default('STJ');
            $table->string('numero_acordao', 120)->nullable();
            $table->text('ementa')->nullable();
            $table->string('relator', 200)->nullable();
            $table->date('data_julgamento')->nullable();
            $table->text('url')->nullable();
            $table->string('tags', 500)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['processo_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_jurisprudencias');
    }
};
