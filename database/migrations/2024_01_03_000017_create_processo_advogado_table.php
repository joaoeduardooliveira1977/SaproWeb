<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_advogado', function (Blueprint $table) {
            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->foreignId('advogado_id')->constrained('pessoas')->cascadeOnDelete();
            $table->primary(['processo_id', 'advogado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_advogado');
    }
};
