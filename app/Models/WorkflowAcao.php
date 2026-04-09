<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowAcao extends Model
{
    protected $table = 'workflow_acoes';

    protected $fillable = [
        'regra_id',
        'ordem',
        'tipo',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'ordem'  => 'integer',
    ];

    public function regra(): BelongsTo
    {
        return $this->belongsTo(WorkflowRegra::class, 'regra_id');
    }
}
