<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoRepasse extends Model
{
    protected $table = 'contrato_repasses';

    protected $fillable = [
        'contrato_id', 'indicador_id', 'tipo_calculo',
        'percentual', 'valor_fixo', 'frequencia', 'status',
    ];

    protected $casts = [
        'percentual' => 'decimal:2',
        'valor_fixo' => 'decimal:2',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function indicador(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'indicador_id');
    }
}
