<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarTrialExpirado
{
    private const ROTAS_LIVRES = [
        'logout',
        'trial.expirado',
        'plano.expirado',
        'tenant.planos',
        'status',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('usuarios')->check()) {
            return $next($request);
        }

        $rotaAtual = $request->route()?->getName() ?? '';

        // Rotas sempre permitidas (saída, suporte, etc.)
        if (in_array($rotaAtual, self::ROTAS_LIVRES)) {
            return $next($request);
        }

        // Rotas master nunca são bloqueadas por trial
        if (str_starts_with($rotaAtual, 'master.')) {
            return $next($request);
        }

        $tenant = tenant();

        if (!$tenant) {
            return $next($request);
        }

        if ($tenant->plano === 'demo' && $tenant->trialExpirado()) {
            return redirect()->route('trial.expirado');
        }

        return $next($request);
    }
}
