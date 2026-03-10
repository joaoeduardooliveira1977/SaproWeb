<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ─── Andamento ─────────────────────────────────
class Andamento extends Model
{
    protected $table = 'andamentos';
    protected $fillable = ['processo_id', 'data', 'descricao', 'usuario_id'];
    protected $casts = ['data' => 'date'];

    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, 'usuario_id'); }
}

// ─── Agenda ────────────────────────────────────
class Agenda extends Model
{
    protected $table = 'agenda';
    protected $fillable = [
        'titulo', 'data_hora', 'local', 'tipo', 'urgente',
        'processo_id', 'responsavel_id', 'concluido', 'observacoes',
    ];
    protected $casts = [
        'data_hora' => 'datetime',
        'urgente'   => 'boolean',
        'concluido' => 'boolean',
    ];

    public function processo(): BelongsTo    { return $this->belongsTo(Processo::class); }
    public function responsavel(): BelongsTo { return $this->belongsTo(Usuario::class, 'responsavel_id'); }

    public function scopeHoje($query)
    {
        return $query->whereDate('data_hora', today());
    }

    public function scopeProximos($query, int $dias = 7)
    {
        return $query->whereBetween('data_hora', [now(), now()->addDays($dias)]);
    }

    public function scopeNaoConcluidos($query)
    {
        return $query->where('concluido', false);
    }
}

// ─── Custa ─────────────────────────────────────
class Custa extends Model
{
    protected $table = 'custas';
    protected $fillable = [
        'processo_id', 'data', 'descricao', 'valor',
        'pago', 'data_pagamento', 'usuario_id',
    ];
    protected $casts = [
        'data'          => 'date',
        'data_pagamento'=> 'date',
        'pago'          => 'boolean',
        'valor'         => 'decimal:2',
    ];

    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, 'usuario_id'); }
}

// ─── Auditoria ─────────────────────────────────
class Auditoria extends Model
{
    protected $table = 'auditorias';
    protected $fillable = [
        'usuario_id', 'login', 'acao', 'tabela',
        'registro_id', 'dados_antes', 'dados_apos', 'ip',
    ];
    protected $casts = [
        'dados_antes' => 'array',
        'dados_apos'  => 'array',
    ];

    public function usuario(): BelongsTo { return $this->belongsTo(Usuario::class, 'usuario_id'); }
}
