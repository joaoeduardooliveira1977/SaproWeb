<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = [
        'tenant_id',
        'escritorio_nome',
        'escritorio_cnpj',
        'escritorio_endereco',
        'escritorio_cidade',
        'escritorio_uf',
        'escritorio_cep',
        'escritorio_telefone',
        'escritorio_email',
        'escritorio_logo_url',
        'pix_chave',
        'pix_tipo',
        'pix_beneficiario',
        'pix_cidade',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'whatsapp_token',
        'nfe_token',
    ];

    public static function doTenant(int $tenantId): self
    {
        return static::firstOrCreate(['tenant_id' => $tenantId]);
    }
}
