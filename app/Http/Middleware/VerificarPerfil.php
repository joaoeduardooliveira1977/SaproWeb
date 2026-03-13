<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarPerfil
{
    // Mapa de permissões por perfil e módulo
    const PERMISSOES = [
        'admin' => '*', // acesso total

        'advogado' => [
            'dashboard', 'processos', 'pessoas', 'agenda',
            'andamentos', 'documentos', 'tjsp', 'relatorios',
            'minha-conta', 'aasp-publicacoes',
        ],

        'estagiario' => [
            'dashboard', 'processos.ver', 'pessoas.ver',
            'agenda.ver', 'andamentos.ver', 'documentos.ver',
            'minha-conta',
        ],

        'financeiro' => [
            'dashboard', 'financeiro', 'honorarios',
            'relatorios', 'minha-conta',
        ],

        'recepcionista' => [
            'dashboard', 'agenda', 'pessoas',
            'processos.ver', 'minha-conta',
        ],
    ];

    public function handle(Request $request, Closure $next, string $modulo = null)
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        // Atualiza último acesso
        \DB::table('usuarios')
            ->where('id', $usuario->id)
            ->update(['ultimo_acesso' => now()]);

        // Verifica se está ativo
        if (isset($usuario->ativo) && !$usuario->ativo) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Sua conta está desativada.');
        }

        if ($modulo === null) {
            return $next($request);
        }

        $perfil = $usuario->perfil ?? 'estagiario';

        // Admin tem acesso total
        if ($perfil === 'admin') {
            return $next($request);
        }

        $permissoes = self::PERMISSOES[$perfil] ?? [];

        // Verifica se tem permissão
        if (in_array($modulo, $permissoes) || in_array($modulo . '.ver', $permissoes)) {
            return $next($request);
        }

        // Sem permissão
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Sem permissão.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar este módulo.');
    }
}
