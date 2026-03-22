<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // Filtro automático por tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = tenant_id() ?? (Auth::check() ? Auth::user()->tenant_id : null);
            if ($tenantId) {
                $builder->where(static::getModel()->getTable() . '.tenant_id', $tenantId);
            }
        });

        // Auto preencher tenant_id ao criar
        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $model->tenant_id = tenant_id() ?? (Auth::check() ? Auth::user()->tenant_id : null);
            }
        });
    }
}
