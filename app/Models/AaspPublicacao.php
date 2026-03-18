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
        'processo_id',
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

    public function processo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }
}
