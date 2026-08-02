<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarModulo
{
    public function handle(Request $request, Closure $next, string $modulo): Response
    {
        $user = auth('usuarios')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Superadmin ignora verificação de módulos
        if ($user->perfil === 'super_admin') {
            return $next($request);
        }

        $tenant = app()->bound('tenant') ? app('tenant') : null;

        if (!$tenant || !$tenant->temModulo($modulo)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Módulo não disponível no seu plano.'], 403);
            }

            return redirect()->route('modulo.indisponivel')
                ->with('modulo_bloqueado', $modulo);
        }

        return $next($request);
    }
}
