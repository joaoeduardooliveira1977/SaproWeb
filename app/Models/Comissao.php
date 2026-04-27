<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;

class Comissao extends Model
{
    use BelongsToTenant;

    protected $table = 'comissoes';

    protected $fillable = [
        'indicador_id', 'pessoa_id', 'origem_tipo', 'origem_id',
        'valor_base', 'percentual', 'valor_comissao',
        'competencia', 'status', 'data_pagamento', 'observacoes',
    ];

    protected $casts = [
        'valor_base'      => 'decimal:2',
        'percentual'      => 'decimal:2',
        'valor_comissao'  => 'decimal:2',
        'competencia'     => 'date',
        'data_pagamento'  => 'date',
    ];

    public function indicador(): BelongsTo
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopePagas($query)
    {
        return $query->where('status', 'pago');
    }
}
