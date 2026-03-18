<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfxLancamento extends Model
{
    protected $table = 'ofx_lancamentos';

    protected $fillable = [
        'importacao_id', 'data', 'valor', 'tipo',
        'descricao', 'fitid',
        'conciliado', 'referencia_tipo', 'referencia_id',
    ];

    protected $casts = [
        'data'       => 'date',
        'valor'      => 'float',
        'conciliado' => 'boolean',
    ];

    public function importacao(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OfxImportacao::class);
    }

    public function isCredito(): bool
    {
        return $this->valor > 0;
    }

    public function isDebito(): bool
    {
        return $this->valor < 0;
    }

    /** Retorna o registro financeiro vinculado (pagamento ou recebimento). */
    public function referenciaModel(): ?Model
    {
        if (! $this->referencia_tipo || ! $this->referencia_id) return null;

        $class = match ($this->referencia_tipo) {
            'pagamentos'   => \App\Models\Pagamento::class,
            'recebimentos' => \App\Models\Recebimento::class,
            default        => null,
        };

        return $class ? $class::find($this->referencia_id) : null;
    }
}
