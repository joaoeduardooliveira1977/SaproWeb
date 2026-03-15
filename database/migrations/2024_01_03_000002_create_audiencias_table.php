<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audiencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('processo_id')->constrained('processos')->cascadeOnDelete();
            $table->foreignId('juiz_id')->nullable()->constrained('pessoas')->nullOnDelete();
            $table->foreignId('advogado_id')->nullable()->constrained('pessoas')->nullOnDelete();
            $table->foreignId('criado_por')->nullable()->constrained('usuarios')->nullOnDelete();

            // Dados da audiência
            $table->dateTime('data_hora');
            $table->enum('tipo', [
                'conciliacao',
                'instrucao',
                'instrucao_julgamento',
                'julgamento',
                'una',
                'outra',
            ])->default('outra');

            $table->string('sala', 100)->nullable();
            $table->string('local', 200)->nullable();
            $table->string('preposto', 150)->nullable(); // representante da parte
            $table->text('pauta')->nullable();

            // Status e resultado
            $table->enum('status', [
                'agendada',
                'realizada',
                'cancelada',
                'redesignada',
            ])->default('agendada');

            $table->enum('resultado', [
                'acordo',
                'condenacao',
                'improcedente',
                'extincao',
                'nao_realizada',
                'outra',
            ])->nullable();

            $table->text('resultado_descricao')->nullable();
            $table->text('proximo_passo')->nullable();
            $table->date('data_proximo')->nullable(); // data da próxima audiência/prazo

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audiencias');
    }
};
