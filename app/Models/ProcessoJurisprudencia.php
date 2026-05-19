<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessoJurisprudencia extends Model
{
    use BelongsToTenant;
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
