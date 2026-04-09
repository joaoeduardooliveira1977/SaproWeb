<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowExecucao extends Model
{
    protected $table = 'workflow_execucoes';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'regra_id',
        'processo_id',
        'gatilho_payload',
        'status',
        'resultado',
        'erro_mensagem',
        'created_at',
    ];

    protected $casts = [
        'gatilho_payload' => 'array',
        'resultado'       => 'array',
        'created_at'      => 'datetime',
    ];

    const STATUS_EXECUTADO = 'executado';
    const STATUS_ERRO      = 'erro';
    const STATUS_IGNORADO  = 'ignorado';

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->created_at ??= now();
        });
    }

    public function regra(): BelongsTo
    {
        return $this->belongsTo(WorkflowRegra::class, 'regra_id');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }
}
