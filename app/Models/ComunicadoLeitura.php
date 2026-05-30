<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComunicadoLeitura extends Model
{
    protected $table = 'comunicado_leituras';

    protected $fillable = ['comunicado_id', 'usuario_id', 'tenant_id', 'lido_em'];

    protected $casts = ['lido_em' => 'datetime'];

    public function comunicado(): BelongsTo
    {
        return $this->belongsTo(Comunicado::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
