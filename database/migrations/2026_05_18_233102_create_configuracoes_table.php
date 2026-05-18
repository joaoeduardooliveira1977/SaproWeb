<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            // Escritório
            $table->string('escritorio_nome')->nullable();
            $table->string('escritorio_cnpj')->nullable();
            $table->string('escritorio_endereco')->nullable();
            $table->string('escritorio_cidade')->nullable();
            $table->string('escritorio_uf', 2)->nullable();
            $table->string('escritorio_cep', 9)->nullable();
            $table->string('escritorio_telefone')->nullable();
            $table->string('escritorio_email')->nullable();
            $table->string('escritorio_logo_url')->nullable();
            // PIX
            $table->string('pix_chave')->nullable();
            $table->string('pix_tipo')->nullable(); // cpf, cnpj, email, telefone, aleatoria
            $table->string('pix_beneficiario')->nullable();
            $table->string('pix_cidade')->nullable();
            // SMTP
            $table->string('smtp_host')->nullable();
            $table->unsignedSmallInteger('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();
            // Integrações
            $table->string('whatsapp_token')->nullable();
            $table->string('nfe_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes');
    }
};
