<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoramentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->string('numero_processo', 50);
            $table->string('tribunal', 20)->nullable();
            $table->date('ultimo_andamento_data')->nullable();
            $table->string('ultimo_andamento_hash', 64)->nullable();
            $table->boolean('ativo')->default(true);
            $table->boolean('notificar_email')->default(true);
            $table->timestamps();
            $table->index(['tenant_id', 'ativo']);
            $table->index('numero_processo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoramentos');
    }
};
