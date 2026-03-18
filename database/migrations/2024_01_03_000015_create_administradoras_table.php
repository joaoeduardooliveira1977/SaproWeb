<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administradoras', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('cnpj', 18)->nullable()->unique();
            $table->string('telefone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('contato', 100)->nullable(); // nome do responsável
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::table('pessoas', function (Blueprint $table) {
            $table->foreignId('administradora_id')
                ->nullable()
                ->after('ativo')
                ->constrained('administradoras')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Administradora::class);
            $table->dropColumn('administradora_id');
        });

        Schema::dropIfExists('administradoras');
    }
};
