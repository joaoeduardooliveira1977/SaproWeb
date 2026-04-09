<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;

class Monitoramento extends Model
{
    use BelongsToTenant;

    protected $table = 'monitoramentos';

    protected $fillable = [
        'tenant_id', 'processo_id', 'numero_processo',
        'tribunal', 'ultimo_andamento_data', 'ultimo_andamento_hash',
        'ativo', 'notificar_email',
    ];

    protected $casts = [
        'ultimo_andamento_data' => 'date',
        'ativo'                 => 'boolean',
        'notificar_email'       => 'boolean',
    ];

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }
}
