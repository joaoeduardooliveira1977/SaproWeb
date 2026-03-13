<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AaspPublicacao extends Model
{
    protected $table = 'aasp_publicacoes';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'codigo_aasp',
        'data',
        'jornal',
        'numero_processo',
        'titulo',
        'texto',
        'numero_publicacao',
    ];

    protected $casts = [
        'data' => 'date',
        'created_at' => 'datetime',
    ];

    public function advogado(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AaspAdvogado::class, 'codigo_aasp', 'codigo_aasp');
    }
}
