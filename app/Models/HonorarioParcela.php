<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HonorarioParcela extends Model
{
    protected $table = 'honorario_parcelas';

    protected $fillable = [
        'honorario_id', 'numero_parcela', 'valor', 'vencimento',
        'data_pagamento', 'valor_pago', 'status', 'forma_pagamento', 'observacoes',
    ];

    protected $casts = [
        'vencimento'      => 'date',
        'data_pagamento'  => 'date',
        'valor'           => 'decimal:2',
        'valor_pago'      => 'decimal:2',
    ];

    public function honorario(): BelongsTo
    {
        return $this->belongsTo(Honorario::class, 'honorario_id');
    }

    public function cobrancas(): HasMany
    {
        return $this->hasMany(Cobranca::class, 'parcela_id');
    }

    public function diasAtraso(): int
    {
        if ($this->status === 'pago') return 0;
        return max(0, now()->diffInDays($this->vencimento, false) * -1);
    }

    public function statusVisual(): string
    {
        $dias = $this->diasAtraso();
        if ($dias === 0) return 'em_dia';
        if ($dias <= 15)  return 'atrasado';
        if ($dias <= 30)  return 'em_cobranca';
        return 'inadimplente';
    }
}
