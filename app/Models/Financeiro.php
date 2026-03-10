<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

// ─── Fornecedor ────────────────────────────────
class Fornecedor extends Model
{
    protected $table    = 'fornecedores';
    protected $fillable = ['nome', 'cnpj_cpf', 'telefone', 'email', 'observacoes', 'ativo'];
    protected $casts    = ['ativo' => 'boolean'];

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'fornecedor_id');
    }

    public function scopeAtivos($query) { return $query->where('ativo', true); }
}

// ─── OrigemRecebimento ─────────────────────────
class OrigemRecebimento extends Model
{
    protected $table    = 'origens_recebimento';
    protected $fillable = ['descricao', 'ativo'];
    protected $casts    = ['ativo' => 'boolean'];

    public function recebimentos(): HasMany
    {
        return $this->hasMany(Recebimento::class, 'origem_id');
    }
}

// ─── Apontamento ───────────────────────────────
class Apontamento extends Model
{
    protected $table    = 'apontamentos';
    protected $fillable = [
        'processo_id', 'advogado_id', 'data', 'descricao',
        'horas', 'valor', 'usuario_id',
    ];
    protected $casts = [
        'data'  => 'date',
        'horas' => 'decimal:2',
        'valor' => 'decimal:2',
    ];

    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function advogado(): BelongsTo { return $this->belongsTo(Pessoa::class, 'advogado_id'); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, 'usuario_id'); }

    // Totais por processo
    public static function totaisPorProcesso(int $processoId): array
    {
        $rows = static::where('processo_id', $processoId)->get();
        return [
            'total_horas' => $rows->sum('horas'),
            'total_valor' => $rows->sum('valor'),
        ];
    }
}

// ─── Pagamento ─────────────────────────────────
class Pagamento extends Model
{
    protected $table    = 'pagamentos';
    protected $fillable = [
        'processo_id', 'fornecedor_id', 'data', 'numero_doc',
        'documento', 'descricao', 'valor', 'valor_pago',
        'data_vencimento', 'data_pagamento', 'pago', 'usuario_id',
    ];
    protected $casts = [
        'data'             => 'date',
        'data_vencimento'  => 'date',
        'data_pagamento'   => 'date',
        'valor'            => 'decimal:2',
        'valor_pago'       => 'decimal:2',
        'pago'             => 'boolean',
    ];

    public function processo():    BelongsTo { return $this->belongsTo(Processo::class); }
    public function fornecedor():  BelongsTo { return $this->belongsTo(Fornecedor::class, 'fornecedor_id'); }
    public function usuario():     BelongsTo { return $this->belongsTo(Usuario::class, 'usuario_id'); }

    public function scopePendentes($query)
    {
        return $query->where('pago', false);
    }

    public function scopeVencendo($query, int $dias = 7)
    {
        return $query->where('pago', false)
                     ->whereBetween('data_vencimento', [today(), today()->addDays($dias)]);
    }

    // Totais por processo
    public static function totaisPorProcesso(int $processoId): array
    {
        $rows = static::where('processo_id', $processoId)->get();
        return [
            'total'    => $rows->sum('valor'),
            'pago'     => $rows->where('pago', true)->sum('valor_pago'),
            'pendente' => $rows->where('pago', false)->sum('valor'),
        ];
    }
}

// ─── Recebimento ───────────────────────────────
class Recebimento extends Model
{
    protected $table    = 'recebimentos';
    protected $fillable = [
        'processo_id', 'origem_id', 'data', 'numero_doc',
        'documento', 'descricao', 'valor', 'valor_recebido',
        'data_recebimento', 'recebido', 'usuario_id',
    ];
    protected $casts = [
        'data'              => 'date',
        'data_recebimento'  => 'date',
        'valor'             => 'decimal:2',
        'valor_recebido'    => 'decimal:2',
        'recebido'          => 'boolean',
    ];

    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function origem():   BelongsTo { return $this->belongsTo(OrigemRecebimento::class, 'origem_id'); }
    public function usuario():  BelongsTo { return $this->belongsTo(Usuario::class, 'usuario_id'); }

    public function scopePendentes($query)
    {
        return $query->where('recebido', false);
    }

    // Totais por processo
    public static function totaisPorProcesso(int $processoId): array
    {
        $rows = static::where('processo_id', $processoId)->get();
        return [
            'total'     => $rows->sum('valor'),
            'recebido'  => $rows->where('recebido', true)->sum('valor_recebido'),
            'pendente'  => $rows->where('recebido', false)->sum('valor'),
        ];
    }
}
