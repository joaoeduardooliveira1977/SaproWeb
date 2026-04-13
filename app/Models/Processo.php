<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};
use App\Models\Traits\BelongsToTenant;


class Processo extends Model
{
    
	use BelongsToTenant;
    
	protected $table = 'processos';

    protected $fillable = [
        'tenant_id', 'numero', 'data_distribuicao', 'extrajudicial', 'cliente_id', 'parte_contraria',
        'parte_contraria_id', 'autor_reu', 'unidade', 'advogado_id',
        'tipo_acao_id', 'tipo_processo_id', 'fase_id', 'risco_id',
        'reparticao_id', 'vara', 'valor_causa', 'valor_risco',
        'observacoes', 'status', 'criado_por',
        'analise_ia', 'analise_ia_em',
        'score', 'resumo_ia', 'monitoramento_ativo', 'frequencia_monitoramento',
        'ultima_verificacao_datajud',
    ];

    protected $casts = [
        'data_distribuicao' => 'date',
        'extrajudicial'     => 'boolean',
        'valor_causa'       => 'decimal:2',
        'valor_risco'       => 'decimal:2',
        'analise_ia_em'              => 'datetime',
        'monitoramento_ativo'        => 'boolean',
        'ultima_verificacao_datajud' => 'datetime',
    ];

    // ── Relacionamentos ────────────────────────────
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    public function parteContraria(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'parte_contraria_id');
    }

    public function advogado(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'advogado_id');
    }

    public function advogados(): BelongsToMany
    {
        return $this->belongsToMany(Pessoa::class, 'processo_advogado', 'processo_id', 'advogado_id');
    }

    public function tipoAcao(): BelongsTo
    {
        return $this->belongsTo(TipoAcao::class, 'tipo_acao_id');
    }

    public function tipoProcesso(): BelongsTo
    {
        return $this->belongsTo(TipoProcesso::class, 'tipo_processo_id');
    }

    public function fase(): BelongsTo
    {
        return $this->belongsTo(Fase::class, 'fase_id');
    }

    public function risco(): BelongsTo
    {
        return $this->belongsTo(GrauRisco::class, 'risco_id');
    }

    public function reparticao(): BelongsTo
    {
        return $this->belongsTo(Reparticao::class, 'reparticao_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'criado_por');
    }

    public function andamentos(): HasMany
    {
        return $this->hasMany(Andamento::class, 'processo_id')->orderByDesc('data');
    }

    public function custas(): HasMany
    {
        return $this->hasMany(Custa::class, 'processo_id')->orderByDesc('data');
    }

    public function agenda(): HasMany
    {
        return $this->hasMany(Agenda::class, 'processo_id')->orderBy('data_hora');
    }

    public function audiencias(): HasMany
    {
        return $this->hasMany(Audiencia::class, 'processo_id')->orderByDesc('data_hora');
    }

    // ── Scopes ─────────────────────────────────────
    public function scopeAtivos($query)
    {
        return $query->where('status', 'Ativo');
    }

    public function scopeBusca($query, string $termo)
	{
    	return $query->where(function ($q) use ($termo) {
        $q->where('numero', 'ilike', "%{$termo}%")
          ->orWhere('parte_contraria', 'ilike', "%{$termo}%")
          ->orWhereHas('cliente',        fn($c) => $c->where('nome', 'ilike', "%{$termo}%"))
          ->orWhereHas('advogado',       fn($a) => $a->where('nome', 'ilike', "%{$termo}%"))
          ->orWhereHas('parteContraria', fn($p) => $p->where('nome', 'ilike', "%{$termo}%"));
    	});
	}

    // ── Acessores ──────────────────────────────────
    public function getTotalCustasAttribute(): float
    {
        return (float) $this->custas->sum('valor');
    }

    public function getCustasPendentesAttribute(): float
    {
        return (float) $this->custas->where('pago', false)->sum('valor');
    }
}
