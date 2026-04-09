<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteVerificacao extends Model
{
    protected $table = 'lote_verificacoes';

    protected $fillable = [
        'processo_numero',
        'status',
        'erro_mensagem',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Usuario::class, 'user_id');
    }
}
