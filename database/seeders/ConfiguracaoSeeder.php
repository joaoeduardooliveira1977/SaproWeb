<?php

namespace Database\Seeders;

use App\Models\Configuracao;
use Illuminate\Database\Seeder;

class ConfiguracaoSeeder extends Seeder
{
    public function run(): void
    {
        Configuracao::updateOrCreate(
            ['tenant_id' => 1],
            [
                'escritorio_nome'   => 'Escritório Modelo Advocacia',
                'escritorio_cidade' => 'São Paulo',
                'escritorio_uf'     => 'SP',
                'pix_chave'         => '+5511953505384',
                'pix_tipo'          => 'telefone',
                'pix_beneficiario'  => 'Escritorio Modelo',
                'pix_cidade'        => 'Sao Paulo',
            ]
        );
    }
}
