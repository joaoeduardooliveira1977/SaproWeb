<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\SoftDeletes;

class DespesaReembolso extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $table = 'despesas_reembolsos';

    protected $fillable = [
        'tenant_id', 'cliente_id', 'data_lancamento', 'tipo', 'descricao',
        'valor', 'status', 'responsavel_id', 'observacoes',
        'aprovado_por', 'aprovado_em', 'financeiro_id', 'financeiro_tipo',
    ];

    protected $casts = [
        'data_lancamento' => 'date',
        'aprovado_em'     => 'datetime',
        'valor'           => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    public function aprovador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aprovado_por');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(DespesaReembolsoAnexo::class, 'despesa_reembolso_id');
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePorCliente($query, int $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopePorPeriodo($query, string $inicio, string $fim)
    {
        return $query->whereBetween('data_lancamento', [$inicio, $fim]);
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'pendente'   => 'Pendente',
            'aprovado'   => 'Aprovado',
            'nao_cobrar' => 'Não cobrar',
            'ja_cobrado' => 'Já cobrado',
            default      => $this->status,
        };
    }

    public function getLabelTipoAttribute(): string
    {
        return $this->tipo === 'reembolso' ? 'Reembolso' : 'Despesa';
    }
}
