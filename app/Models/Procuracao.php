<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\Traits\BelongsToTenant;

class Procuracao extends Model
{
    use BelongsToTenant;

    protected $table = 'procuracoes';

    protected $fillable = [
        'cliente_id', 'processo_id', 'tipo', 'data_emissao',
        'data_validade', 'poderes', 'arquivo', 'observacoes', 'ativa',
    ];

    protected $casts = [
        'data_emissao'  => 'date',
        'data_validade' => 'date',
        'ativa'         => 'boolean',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public function statusVencimento(): string
    {
        if (! $this->data_validade) {
            return 'indeterminado';
        }
        $dias = now()->diffInDays($this->data_validade, false);
        if ($dias < 0)    return 'vencida';
        if ($dias <= 30)  return 'vencendo';
        return 'vigente';
    }

    public function statusCor(): string
    {
        return match ($this->statusVencimento()) {
            'vencida'    => '#dc2626',
            'vencendo'   => '#d97706',
            default      => '#16a34a',
        };
    }
}
