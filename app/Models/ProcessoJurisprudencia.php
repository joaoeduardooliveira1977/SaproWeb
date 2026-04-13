<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessoJurisprudencia extends Model
{
    protected $table = 'processo_jurisprudencias';

    protected $fillable = [
        'processo_id', 'tenant_id', 'user_id',
        'tribunal', 'numero_acordao', 'ementa',
        'relator', 'data_julgamento', 'url', 'tags', 'observacoes',
    ];

    protected $casts = [
        'data_julgamento' => 'date',
    ];

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }
}
