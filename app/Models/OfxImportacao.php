<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfxImportacao extends Model
{
    protected $table = 'ofx_importacoes';

    protected $fillable = [
        'arquivo', 'banco', 'agencia', 'conta',
        'data_ini', 'data_fim',
        'total_lancamentos', 'conciliados',
        'usuario_id',
    ];

    protected $casts = [
        'data_ini' => 'date',
        'data_fim' => 'date',
    ];

    public function lancamentos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OfxLancamento::class, 'importacao_id');
    }

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function atualizarConciliados(): void
    {
        $this->update([
            'conciliados' => $this->lancamentos()->where('conciliado', true)->count(),
        ]);
    }

    public function getPendentesAttribute(): int
    {
        return $this->total_lancamentos - $this->conciliados;
    }
}
