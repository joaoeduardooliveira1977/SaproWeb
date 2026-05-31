<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Tenant;

class IdentificarTenant
{
    // Domínio principal da aplicação (sem subdomínio).
    // Requisições deste host carregam o tenant padrão (slug 'demo' ou
    // o tenant cujo campo `dominio` bate exatamente com este valor).
    private const DOMINIO_PRINCIPAL = 'kmd-ia.com.br';

    public function handle(Request $request, Closure $next)
    {
        // Rotas do painel master nunca precisam de tenant de escritório
        if ($this->isMasterRoute($request)) {
            return $next($request);
        }

        // Rota de login: detecta tenant pelo domínio para carregar branding.
        // Não bloqueia se não encontrar — o login funciona sem tenant.
        if ($request->route()?->getName() === 'login') {
            $tenant = $this->detectarPorDominio($request);
            if ($tenant) {
                app()->instance('tenant', $tenant);
                view()->share('tenant', $tenant);
            }
            return $next($request);
        }

        // Rotas públicas sem necessidade de tenant
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
    // Prioridade: campo `dominio` (match exato) → slug do subdomínio →
    // para o domínio principal sem subdomínio, carrega o tenant padrão.
    private function detectarPorDominio(Request $request): ?Tenant
    {
        $host  = $request->getHost();
        $parts = explode('.', $host);

        return Cache::remember("tenant_host_{$host}", now()->addMinutes(10), function () use ($host, $parts) {
            // 1. Match exato pelo campo `dominio` (ex: "meuescritorio.com.br")
            $tenant = Tenant::ativo()->where('dominio', $host)->first();
            if ($tenant) return $tenant;

            // 2. Subdomínio: ≥3 partes (ex: "slug.kmd-ia.com.br")
            if (count($parts) >= 3) {
                $subdomain = $parts[0];
                return Tenant::ativo()->where('slug', $subdomain)->first();
            }

            // 3. Domínio principal sem subdomínio → tenant padrão (demo)
            // Cobre PWA instalados em kmd-ia.com.br e acesso direto ao domínio principal.
            if ($host === self::DOMINIO_PRINCIPAL) {
                return Tenant::ativo()
                    ->where(fn($q) => $q->where('slug', 'demo')->orWhere('dominio', $host))
                    ->orderByRaw("CASE WHEN dominio = ? THEN 0 ELSE 1 END", [$host])
                    ->first();
            }

            return null;
        });
    }

    // Verifica se a rota pertence ao painel master (/master/*).
    private function isMasterRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName() ?? '';
        return str_starts_with($routeName, 'master.');
    }
}
