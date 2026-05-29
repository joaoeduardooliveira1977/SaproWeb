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
        // Para a rota de login: detecta o tenant pelo subdomínio apenas para
        // carregar o branding (logo, cores). Não bloqueia se não encontrar.
        if ($request->route()?->getName() === 'login') {
            $tenant = $this->detectarPorSubdominio($request);
            if ($tenant) {
                app()->instance('tenant', $tenant);
                view()->share('tenant', $tenant);
            }
            return $next($request);
        }

        // Rotas públicas que não precisam de tenant
        $rotasLivres = [
            'registro', 'registro.store', 'tenant.planos', 'logout',
            'super-admin.index', 'super-admin.show', 'super-admin.plano',
            'super-admin.toggle', 'super-admin.login-tenant',
            'super-admin.voltar', 'super-admin.excluir', 'super-admin.criar',
            'super-admin.salvar',
        ];

        if (in_array($request->route()?->getName(), $rotasLivres)) {
            return $next($request);
        }

        // Usuário não autenticado — deixa passar (o guard de rota cuida disso)
        if (!Auth::guard('usuarios')->check()) {
            return $next($request);
        }

        $usuario = Auth::guard('usuarios')->user();

        // Super admin não tem tenant vinculado
        if ($usuario->perfil === 'super_admin') {
            return $next($request);
        }

        if (!$usuario->tenant_id) {
            return $next($request);
        }

        // Busca e cacheia o tenant do usuário autenticado
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

        app()->instance('tenant', $tenant);
        view()->share('tenant', $tenant);

        return $next($request);
    }

    // Detecta o tenant a partir do host da requisição.
    // Tenta pelo campo `dominio` primeiro (match exato), depois pelo `slug`
    // extraído do subdomínio (ex: "escritorio1" em "escritorio1.kmd-ia.com.br").
    private function detectarPorSubdominio(Request $request): ?Tenant
    {
        $host  = $request->getHost();
        $parts = explode('.', $host);

        return Cache::remember("tenant_host_{$host}", now()->addMinutes(10), function () use ($host, $parts) {
            // 1. Tenta match exato pelo campo dominio
            $tenant = Tenant::ativo()->where('dominio', $host)->first();
            if ($tenant) return $tenant;

            // 2. Para subdomínios (≥3 partes), tenta pelo slug
            if (count($parts) >= 3) {
                $subdomain = $parts[0];
                return Tenant::ativo()->where('slug', $subdomain)->first();
            }

            return null;
        });
    }
}
