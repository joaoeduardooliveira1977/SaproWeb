<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantModulo extends Model
{
    protected $fillable = [
        'tenant_id', 'modulo_id', 'ativo',
        'ativado_por', 'ativado_em', 'observacao',
    ];

    protected $casts = [
        'ativo'      => 'boolean',
        'ativado_em' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }

    public function ativadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ativado_por');
    }
}
