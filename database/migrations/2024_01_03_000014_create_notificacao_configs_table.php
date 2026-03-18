<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacao_configs', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 40)->unique();   // prazo_fatal | prazo_vencendo | audiencia | cobranca
            $table->string('label', 100);
            $table->boolean('ativo')->default(true);
            $table->json('antecedencias');           // [1, 3, 7]
            $table->string('canal', 10)->default('whatsapp'); // whatsapp | sms | ambos
            $table->timestamps();
        });

        // Defaults
        $now = now();
        DB::table('notificacao_configs')->insert([
            ['tipo' => 'prazo_fatal',    'label' => 'Prazos fatais',         'ativo' => true, 'antecedencias' => json_encode([1, 3]),    'canal' => 'whatsapp', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'prazo_vencendo', 'label' => 'Prazos normais',        'ativo' => true, 'antecedencias' => json_encode([1, 3, 7]), 'canal' => 'whatsapp', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'audiencia',      'label' => 'Audiências',             'ativo' => true, 'antecedencias' => json_encode([1]),       'canal' => 'whatsapp', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'cobranca',       'label' => 'Cobranças de honorários','ativo' => true, 'antecedencias' => json_encode([3, 7, 15]),'canal' => 'whatsapp', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacao_configs');
    }
};
