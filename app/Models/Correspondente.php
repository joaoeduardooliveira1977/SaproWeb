<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Correspondente extends Model
{
    protected $table = 'correspondentes';

    protected $fillable = [
        'processo_id', 'advogado_id', 'solicitado_por',
        'comarca', 'estado', 'tipo', 'descricao',
        'data_solicitacao', 'data_prazo', 'data_realizado',
        'valor_combinado', 'valor_pago', 'data_pagamento',
        'status', 'observacoes',
    ];

    protected $casts = [
        'data_solicitacao' => 'date',
        'data_prazo'       => 'date',
        'data_realizado'   => 'date',
        'data_pagamento'   => 'date',
        'valor_combinado'  => 'decimal:2',
        'valor_pago'       => 'decimal:2',
    ];

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public function advogado(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'advogado_id');
    }

    public function solicitadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'solicitado_por');
    }

    public static function tiposLabel(): array
    {
        return [
            'audiencia'  => 'Audiência',
            'protocolo'  => 'Protocolo',
            'citacao'    => 'Citação/Intimação',
            'pericia'    => 'Perícia',
            'diligencia' => 'Diligência',
            'outro'      => 'Outro',
        ];
    }

    public static function statusLabel(): array
    {
        return [
            'pendente'  => 'Pendente',
            'aceito'    => 'Aceito',
            'realizado' => 'Realizado',
            'pago'      => 'Pago',
            'cancelado' => 'Cancelado',
        ];
    }

    public function tipoLabel(): string
    {
        return static::tiposLabel()[$this->tipo] ?? $this->tipo;
    }

    public function statusCor(): string
    {
        return match($this->status) {
            'pendente'  => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'aceito'    => 'bg-blue-100 text-blue-800 border-blue-200',
            'realizado' => 'bg-purple-100 text-purple-800 border-purple-200',
            'pago'      => 'bg-green-100 text-green-800 border-green-200',
            'cancelado' => 'bg-gray-100 text-gray-500 border-gray-200',
            default     => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }

    public function proximoStatus(): ?string
    {
        return match($this->status) {
            'pendente'  => 'aceito',
            'aceito'    => 'realizado',
            'realizado' => 'pago',
            default     => null,
        };
    }
}
