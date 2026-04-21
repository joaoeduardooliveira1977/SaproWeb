<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Honorario extends Model
{
    protected $table = 'honorarios';

    protected $fillable = [
        'processo_id', 'contrato_id', 'cliente_id', 'tipo', 'descricao',
        'valor_contrato', 'percentual_exito', 'total_parcelas',
        'data_inicio', 'data_fim', 'status', 'observacoes',
    ];

    protected $casts = [
        'data_inicio'     => 'date',
        'data_fim'        => 'date',
        'valor_contrato'  => 'decimal:2',
        'percentual_exito'=> 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function parcelas(): HasMany
    {
        return $this->hasMany(HonorarioParcela::class, 'honorario_id');
    }
}
