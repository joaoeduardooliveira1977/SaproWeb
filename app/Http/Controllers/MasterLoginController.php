<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{MasterAdminLog, MasterTentativaLogin, Usuario};

class MasterLoginController extends Controller
{
    const MAX_TENTATIVAS = 5;
    const BLOQUEIO_MINUTOS = 30;

    public function showLogin()
    {
        if (Auth::guard('usuarios')->check()) {
            $user = Auth::guard('usuarios')->user();
            if ($user->perfil === 'super_admin') {
                return redirect()->route('master.dashboard');
            }
        }
        return view('master.login');
    }

    public function login(Request $request)
    {
        $ip = $request->ip();

        // Verifica bloqueio de IP
        if (MasterTentativaLogin::estaBloqueado($ip)) {
            return back()->withErrors(['login' => 'Muitas tentativas. IP bloqueado por 30 minutos.'])
                         ->withInput($request->only('login'));
        }

        $credentials = $request->validate([
            'login' => 'required|string',
            'senha' => 'required|string',
        ], [
            'login.required' => 'Informe o login ou e-mail.',
            'senha.required' => 'Informe a senha.',
        ]);

        // Localiza por login ou e-mail
        $usuario = Usuario::where('login', $credentials['login'])
            ->orWhere('email', $credentials['login'])
            ->first();

        if (!$usuario || !$usuario->ativo) {
            $this->registrarFalha($ip, $request, 'login_usuario_invalido');
            return back()->withErrors(['login' => 'Usuário não encontrado ou inativo.'])->withInput($request->only('login'));
        }

        if ($usuario->perfil !== 'super_admin') {
            $this->registrarFalha($ip, $request, 'login_sem_permissao', $usuario->id);
            return back()->withErrors(['login' => 'Acesso negado. Esta área é exclusiva para administradores do sistema.'])->withInput($request->only('login'));
        }

        if (!Auth::guard('usuarios')->attempt(['login' => $usuario->login, 'password' => $credentials['senha']])) {
            $this->registrarFalha($ip, $request, 'login_senha_invalida', $usuario->id);
            return back()->withErrors(['login' => 'Senha incorreta.'])->withInput($request->only('login'));
        }

        // Login bem-sucedido
        $request->session()->regenerate();
        MasterTentativaLogin::resetar($ip);
        $usuario->update(['ultimo_acesso' => now()]);
        session(['master_last_activity' => now()]);
        session()->forget('master_2fa_verificado');

        MasterAdminLog::registrar('login_master', null, null, "Login via IP {$ip}", 'login_ok');

        // Se tem 2FA → vai verificar
        if ($usuario->master_2fa_ativo) {
            return redirect()->route('master.2fa.verificar');
        }

        return redirect()->intended(route('master.dashboard'));
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('usuarios')->user();
        if ($user) {
            MasterAdminLog::registrar('logout_master', null, null, null, 'logout');
        }

        Auth::guard('usuarios')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('master.login');
    }

    // ── Helpers privados ──────────────────────────────────────────

    private function registrarFalha(string $ip, Request $request, string $contexto, ?int $usuarioId = null): void
    {
        MasterTentativaLogin::registrarTentativa($ip);
        $bloqueado = MasterTentativaLogin::bloquearSe5Tentativas($ip);

        // Log sem autenticação
        \App\Models\MasterAdminLog::create([
            'admin_id'   => $usuarioId,
            'admin_nome' => $request->input('login', 'desconhecido'),
            'acao'       => 'login_falha',
            'contexto'   => $contexto,
            'detalhes'   => $bloqueado ? "IP bloqueado após 5 tentativas." : null,
            'ip'         => $ip,
            'user_agent' => substr($request->userAgent() ?? '', 0, 300),
        ]);
    }
}
