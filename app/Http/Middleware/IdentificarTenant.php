<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Tenant;

class IdentificarTenant
{
    public function handle(Request $request, Closure $next)
    {
        // Rotas que não precisam de tenant
        $rotasLivres = ['login', 'registro', 'registro.store', 'tenant.planos', 'logout', 'super-admin.index', 'super-admin.show', 'super-admin.plano', 'super-admin.toggle', 'super-admin.login-tenant', 'super-admin.voltar', 'super-admin.excluir'];

        if (in_array($request->route()?->getName(), $rotasLivres)) {
            return $next($request);
        }

        // Se não está autenticado deixa passar
        if (!Auth::guard('usuarios')->check()) {
            return $next($request);
        }

        $usuario = Auth::guard('usuarios')->user();

        // Super admin não tem tenant
        if ($usuario->perfil === 'super_admin') {
            return $next($request);
        }

        if (!$usuario->tenant_id) {
            return $next($request);
        }

        // Busca o tenant do usuário
        $tenant = Cache::remember(
            "tenant_{$usuario->tenant_id}",
            now()->addMinutes(10),
            fn() => Tenant::find($usuario->tenant_id)
        );

        if (!$tenant) {
            Auth::guard('usuarios')->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Escritório não encontrado.']);
        }

        if (!$tenant->ativo) {
            Auth::guard('usuarios')->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Conta suspensa. Entre em contato com o suporte.']);
        }

        if ($tenant->trialExpirado()) {
            return redirect()->route('tenant.planos');
        }

        // Disponibilizar o tenant globalmente
        app()->instance('tenant', $tenant);
        view()->share('tenant', $tenant);

        return $next($request);
    }
}