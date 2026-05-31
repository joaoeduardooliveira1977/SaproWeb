<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterAuth
{
    const SESSION_TIMEOUT_MINUTES = 30;

    public function handle(Request $request, Closure $next)
    {
        // Não autenticado → login master
        if (!Auth::guard('usuarios')->check()) {
            return redirect()->route('master.login');
        }

        $user = Auth::guard('usuarios')->user();

        // Não é superadmin → nega acesso
        if ($user->perfil !== 'super_admin') {
            Auth::guard('usuarios')->logout();
            return redirect()->route('master.login')
                ->with('error', 'Acesso negado. Área exclusiva para administradores do sistema.');
        }

        // Session timeout: 30 minutos de inatividade
        $ultimaAtividade = session('master_last_activity');
        if ($ultimaAtividade && now()->diffInMinutes($ultimaAtividade) >= self::SESSION_TIMEOUT_MINUTES) {
            Auth::guard('usuarios')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('master.login')
                ->with('error', 'Sessão expirada por inatividade. Faça login novamente.');
        }
        session(['master_last_activity' => now()]);

        // 2FA: se ativo e não verificado nesta sessão
        if ($user->master_2fa_ativo && !session('master_2fa_verificado')) {
            // Permite acesso à rota de verificação do 2FA
            if ($request->routeIs('master.2fa.verificar') || $request->routeIs('master.2fa.verificar.post')) {
                return $next($request);
            }
            return redirect()->route('master.2fa.verificar');
        }

        // 2FA não configurado: redireciona para configurar (exceto se já estiver na rota de configuração)
        if (!$user->master_2fa_ativo && !$request->routeIs('master.2fa.configurar*')) {
            // Permite acesso mas mostra aviso no dashboard
            session(['master_2fa_aviso' => true]);
        }

        return $next($request);
    }
}
