<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('indices_monetarios')) {
            return;
        }

        Schema::create('indices_monetarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);          // Ex: "IPCA - IBGE"
            $table->string('sigla', 10);          // Ex: "IPCA"
            $table->date('mes_ref');              // Primeiro dia do mês de referência
            $table->decimal('percentual', 8, 4); // Variação mensal em % (ex: 0.4200)
            $table->timestamps();

            $table->unique(['sigla', 'mes_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indices_monetarios');
    }
};
