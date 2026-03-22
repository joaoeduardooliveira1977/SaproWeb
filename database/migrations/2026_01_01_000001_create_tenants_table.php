<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->string('email')->unique();
            $table->string('telefone')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('logo')->nullable();

            // Plano
            $table->enum('plano', ['demo', 'starter', 'pro', 'enterprise'])->default('demo');
            $table->timestamp('trial_expira_em')->nullable();
            $table->boolean('ativo')->default(true);

            // Limites por plano
            $table->integer('limite_processos')->default(5);
            $table->integer('limite_usuarios')->default(2);
            $table->boolean('ia_habilitada')->default(false);
            $table->boolean('datajud_habilitado')->default(false);
            $table->boolean('whatsapp_habilitado')->default(false);

            // Configurações
            $table->string('timezone')->default('America/Sao_Paulo');
            $table->string('gemini_api_key')->nullable();
            $table->json('configuracoes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
