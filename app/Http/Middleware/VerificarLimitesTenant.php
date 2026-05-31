<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Cache};

class VerificarLimitesTenant
{
    public function handle(Request $request, Closure $next)
    {
        $usuario = Auth::guard('usuarios')->user();

        if (!$usuario || $usuario->perfil === 'super_admin') {
            return $next($request);
        }

        $tenant = Cache::remember("tenant_{$usuario->tenant_id}", 300, function () use ($usuario) {
            return \App\Models\Tenant::find($usuario->tenant_id);
        });

        if (!$tenant) {
            return $next($request);
        }

        // Verifica trial expirado
        if ($tenant->plano === 'demo' && $tenant->trial_expira_em?->isPast()) {
            Auth::guard('usuarios')->logout();
            return redirect()->route('plano.expirado')->with('motivo', 'trial');
        }

        // Verifica plano expirado
        if ($tenant->plano_expira_em?->isPast()) {
            Auth::guard('usuarios')->logout();
            return redirect()->route('plano.expirado')->with('motivo', 'plano');
        }

        return $next($request);
    }
}
