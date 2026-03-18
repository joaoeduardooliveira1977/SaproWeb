<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmAtividade extends Model
{
    protected $table = 'crm_atividades';

    protected $fillable = [
        'oportunidade_id', 'tipo', 'descricao',
        'data_prevista', 'data_realizada', 'concluida', 'usuario_id',
    ];

    protected $casts = [
        'data_prevista'  => 'date',
        'data_realizada' => 'date',
        'concluida'      => 'boolean',
    ];

    public static array $tipos = [
        'ligacao'  => ['label' => 'Ligação',  'icon' => '📞'],
        'reuniao'  => ['label' => 'Reunião',  'icon' => '🤝'],
        'email'    => ['label' => 'E-mail',   'icon' => '✉️'],
        'whatsapp' => ['label' => 'WhatsApp', 'icon' => '💬'],
        'tarefa'   => ['label' => 'Tarefa',   'icon' => '✅'],
    ];

    public function oportunidade(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CrmOportunidade::class, 'oportunidade_id');
    }

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function isAtrasada(): bool
    {
        return ! $this->concluida && $this->data_prevista && $this->data_prevista->isPast();
    }
}
