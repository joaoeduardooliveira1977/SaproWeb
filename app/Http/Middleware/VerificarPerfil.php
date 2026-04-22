<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Cache, DB};

class VerificarPerfil
{
    const PERMISSOES = [
        'admin'         => '*',
        'administrador' => '*',
        'super_admin'   => '*',
        'advogado' => [
            'geral', 'processos', 'pessoas', 'documentos', 'minutas',
            'financeiro', 'honorarios', 'relatorios',
            'ferramentas', 'usuarios', 'aasp-publicacoes',
        ],
        'estagiario' => [
            'geral', 'processos', 'pessoas', 'documentos',
        ],
        'financeiro' => [
            'geral', 'financeiro', 'honorarios', 'relatorios', 'pessoas',
        ],
        'recepcionista' => [
            'geral', 'pessoas', 'processos',
        ],
    ];

    const ACOES = [
        'admin'         => '*',
        'administrador' => '*',
        'super_admin'   => '*',
        'advogado'      => ['processos.editar', 'processos.arquivar', 'pessoas.editar', 'pessoas.desativar', 'prazos.editar', 'prazos.excluir', 'agenda.editar', 'agenda.excluir'],
        'estagiario'    => ['processos.ver', 'pessoas.ver'],
        'financeiro'    => ['financeiro.editar'],
        'recepcionista' => ['agenda.editar', 'pessoas.editar'],
    ];

    public function handle(Request $request, Closure $next, string $modulo = null)
    {
        $usuario = auth('usuarios')->user() ?? auth()->user();

        if (! $usuario) {
            return redirect()->route('login');
        }

        DB::table('usuarios')->where('id', $usuario->id)->update(['ultimo_acesso' => now()]);

        if (isset($usuario->ativo) && ! $usuario->ativo) {
            auth('usuarios')->logout();
            auth()->logout();
            return redirect()->route('login')->with('error', 'Sua conta está desativada.');
        }

        // Redireciona para o onboarding se ainda não foi concluído
        if (! $request->routeIs('onboarding') && $usuario->tenant_id) {
            $onboardingConcluido = Cache::remember("onboarding.{$usuario->tenant_id}", 300, function () use ($usuario) {
                return DB::table('tenants')->where('id', $usuario->tenant_id)->value('onboarding_concluido');
            });
            if (! $onboardingConcluido) {
                return redirect()->route('onboarding');
            }
        }

        if ($modulo === null) {
            return $next($request);
        }

        $perfil   = $usuario->perfil ?? 'estagiario';
        $tenantId = $usuario->tenant_id ?? null;

        if (in_array($perfil, ['admin', 'administrador', 'super_admin'])) {
            return $next($request);
        }

        if ($modulo === 'admin') {
            return redirect()->route('dashboard')->with('error', 'Acesso restrito ao administrador.');
        }

        // Verifica override no banco (cache 5 min por tenant+perfil)
        if ($tenantId) {
            $override = Cache::remember("perfil_perm.{$tenantId}.{$perfil}.{$modulo}", 300, function () use ($tenantId, $perfil, $modulo) {
                $row = DB::table('perfil_permissoes')
                    ->where('tenant_id', $tenantId)
                    ->where('perfil', $perfil)
                    ->where('modulo', $modulo)
                    ->first();
                return $row ? (bool) $row->permitido : null;
            });

            if ($override === true)  return $next($request);
            if ($override === false) return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar este módulo.');
        }

        // Fallback para permissões padrão
        $permissoes = self::PERMISSOES[$perfil] ?? [];

        if ($permissoes === '*' || in_array($modulo, $permissoes)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Sem permissão.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar este módulo.');
    }
}
