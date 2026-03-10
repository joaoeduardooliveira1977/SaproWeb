<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TjspVerificacao extends Model
{
    protected $table = 'tjsp_verificacoes';

    protected $fillable = [
        'status',
        'total',
        'processado',
        'processo_atual',
        'novos_total',
        'novos_andamentos',
        'iniciado_em',
        'concluido_em',
    ];

    protected $casts = [
        'novos_andamentos' => 'array',
        'iniciado_em'      => 'datetime',
        'concluido_em'     => 'datetime',
    ];

    public function porcentagem(): int
    {
        if ($this->total === 0) return 0;
        return (int) round(($this->processado / $this->total) * 100);
    }

    public function emAndamento(): bool
    {
        return in_array($this->status, ['pendente', 'rodando']);
    }
}
