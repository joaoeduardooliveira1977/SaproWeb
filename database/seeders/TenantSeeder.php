<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::firstOrCreate(
            ['slug' => 'demo'],
            [
                'nome'          => 'SAPRO — Sistema Jurídico',
                'email'         => 'demo@sapro.com.br',
                'dominio'       => 'kmd-ia.com.br',
                'cor_primaria'  => '#1a3a5c',
                'cor_secundaria'=> '#c9a84c',
                'plano'         => 'demo',
                'ativo'         => true,
                'timezone'      => 'America/Sao_Paulo',
                'limite_processos' => 5,
                'limite_usuarios'  => 2,
                'ia_habilitada'    => false,
                'datajud_habilitado' => false,
                'whatsapp_habilitado'=> false,
                'trial_expira_em' => now()->addDays(30),
            ]
        );

        $this->command->info('✅ TenantSeeder: tenant demo criado/verificado.');
    }
}
