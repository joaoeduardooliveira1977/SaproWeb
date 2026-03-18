<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacaoConfig extends Model
{
    protected $table = 'notificacao_configs';

    protected $fillable = ['tipo', 'label', 'ativo', 'antecedencias', 'canal'];

    protected $casts = [
        'ativo'         => 'boolean',
        'antecedencias' => 'array',
    ];

    /** Retorna config de um tipo, ou defaults se não existir. */
    public static function para(string $tipo): self
    {
        return static::firstOrCreate(
            ['tipo' => $tipo],
            ['label' => $tipo, 'ativo' => true, 'antecedencias' => [1, 3], 'canal' => 'whatsapp']
        );
    }

    /** Retorna todas as configs indexadas por tipo. */
    public static function indexadas(): array
    {
        return static::all()->keyBy('tipo')->toArray();
    }
}
