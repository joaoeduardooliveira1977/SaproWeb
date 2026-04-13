<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('administradoras')) {
            Schema::create('administradoras', function (Blueprint $table) {
                $table->id();
                $table->string('nome', 150);
                $table->string('cnpj', 18)->nullable()->unique();
                $table->string('telefone', 30)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('contato', 100)->nullable(); // nome do responsavel
                $table->text('observacoes')->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pessoas') || Schema::hasColumn('pessoas', 'administradora_id')) {
            return;
        }

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
        if (Schema::hasTable('pessoas') && Schema::hasColumn('pessoas', 'administradora_id')) {
            Schema::table('pessoas', function (Blueprint $table) {
                $table->dropConstrainedForeignId('administradora_id');
            });
        }

        Schema::dropIfExists('administradoras');
    }
};
