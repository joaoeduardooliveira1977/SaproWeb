<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_tentativas_login', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45)->index();
            $table->integer('tentativas')->default(0);
            $table->timestamp('bloqueado_ate')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_tentativas_login');
    }
};
