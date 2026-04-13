<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarPerfil
{
    // Mapa de permissões por perfil e módulo
    const PERMISSOES = [
	
	'admin'         => '*',
    	'administrador' => '*',  // ← adicionar esta linha
    	'super_admin'   => '*',  // ← adicionar esta linha
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

    // Ações permitidas por perfil (para guards em Livewire)
    const ACOES = [
        'admin'       => '*',
        'administrador' => '*',
        'super_admin'   => '*',
        'advogado'    => ['processos.editar', 'processos.arquivar', 'pessoas.editar', 'pessoas.desativar', 'prazos.editar', 'prazos.excluir', 'agenda.editar', 'agenda.excluir'],
        'estagiario'  => ['processos.ver', 'pessoas.ver'],
        'financeiro'  => ['financeiro.editar'],
        'recepcionista'=> ['agenda.editar', 'pessoas.editar'],
    ];

    public function handle(Request $request, Closure $next, string $modulo = null)
    {
        $usuario = auth('usuarios')->user() ?? auth()->user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        // Atualiza último acesso
        \DB::table('usuarios')
            ->where('id', $usuario->id)
            ->update(['ultimo_acesso' => now()]);

        // Verifica se está ativo
        if (isset($usuario->ativo) && !$usuario->ativo) {
            auth('usuarios')->logout();
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

        // Módulos exclusivos do admin
        if ($modulo === 'admin') {
            return redirect()->route('dashboard')->with('error', 'Acesso restrito ao administrador.');
        }

        $permissoes = self::PERMISSOES[$perfil] ?? [];

        // Se tem acesso total
	if ($permissoes === '*') {
    	return $next($request);
	}


	// Verifica se tem permissão
        if (in_array($modulo, $permissoes)) {
            return $next($request);
        }

        // Sem permissão
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Sem permissão.'], 403);
        }

       
	if (in_array($perfil, ['admin', 'administrador', 'super_admin'])) {
    	return $next($request);
	}


 return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar este módulo.');
    }
}
