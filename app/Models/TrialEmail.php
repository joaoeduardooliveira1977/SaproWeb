<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialEmail extends Model
{
    protected $table = 'trial_emails';

    public $timestamps = false;

    protected $fillable = ['tenant_id', 'tipo', 'enviado_em', 'sucesso'];

    protected $casts = [
        'enviado_em' => 'datetime',
        'sucesso'    => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
