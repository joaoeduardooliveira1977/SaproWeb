<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // 🔒 FILTRO AUTOMÁTICO
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check() && !is_null(Auth::user()->tenant_id)) {
    	$builder->where('tenant_id', Auth::user()->tenant_id);
	}
        });

        // 🧠 AUTO PREENCHER tenant_id
        static::creating(function ($model) {
            if (Auth::check() && !is_null(Auth::user()->tenant_id)) {
    	$builder->where('tenant_id', Auth::user()->tenant_id);
	}
        });
    }
}