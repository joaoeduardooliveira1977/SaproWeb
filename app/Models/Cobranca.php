<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cobranca extends Model
{
    protected $table = 'cobrancas';

    protected $fillable = [
        'parcela_id', 'cliente_id', 'usuario_id',
        'tipo', 'data', 'descricao',
    ];

    protected $casts = ['data' => 'date'];

    public function parcela(): BelongsTo
    {
        return $this->belongsTo(HonorarioParcela::class, 'parcela_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public static function tiposLabel(): array
    {
        return [
            'email'      => 'E-mail',
            'ligacao'    => 'Ligação',
            'whatsapp'   => 'WhatsApp',
            'reuniao'    => 'Reunião',
            'negociacao' => 'Negociação',
            'acordo'     => 'Acordo',
        ];
    }

    public function tipoLabel(): string
    {
        return static::tiposLabel()[$this->tipo] ?? $this->tipo;
    }
}
