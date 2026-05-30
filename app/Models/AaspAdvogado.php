<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AaspAdvogado extends Model
{
    use BelongsToTenant;

    protected $table = 'aasp_advogados';

    protected $fillable = [
        'tenant_id',
        'nome',
        'codigo_aasp',
        'chave_aasp',
        'email',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];
}
