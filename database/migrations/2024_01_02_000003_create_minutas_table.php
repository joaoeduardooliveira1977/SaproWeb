<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minutas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->string('categoria', 50)->default('outros');
            $table->text('corpo');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minutas');
    }
};
