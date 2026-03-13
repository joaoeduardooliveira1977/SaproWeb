<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AaspAdvogado extends Model
{
    protected $table = 'aasp_advogados';

    protected $fillable = [
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
