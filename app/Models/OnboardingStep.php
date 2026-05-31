<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingStep extends Model
{
    protected $table = 'onboarding_steps';

    protected $fillable = ['tenant_id', 'step', 'concluido', 'concluido_em'];

    protected $casts = [
        'concluido'    => 'boolean',
        'concluido_em' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
