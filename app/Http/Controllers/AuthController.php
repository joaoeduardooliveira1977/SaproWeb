<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\Usuario;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS     = 5;
    private const DECAY_SECONDS    = 900; // 15 minutos

    public function showLogin()
    {
        if (Auth::guard('usuarios')->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'senha' => 'required|string',
        ], [
            'login.required' => 'Informe o login.',
            'senha.required' => 'Informe a senha.',
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutos = ceil($seconds / 60);

            Log::warning('Login bloqueado por excesso de tentativas', [
                'ip'    => $request->ip(),
                'login' => $credentials['login'],
            ]);

            return back()->withErrors([
                'login' => "Muitas tentativas. Tente novamente em {$minutos} minuto(s).",
            ]);
        }

        $usuario = Usuario::where('login', $credentials['login'])->first();

        if (!$usuario) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);
            return back()->withErrors(['login' => 'Usuário não encontrado ou inativo.']);
        }

        // Aguardando verificação de email (cadastro pela landing)
        if (!$usuario->email_verificado && $usuario->email_token_verificacao) {
            session([
                'verificacao_usuario_id' => $usuario->id,
                'verificacao_email'      => $usuario->email,
                'verificacao_tentativas' => 0,
            ]);
            return redirect()->route('verificar-email')
                ->with('info', "Seu email ainda não foi verificado. Enviamos um código para {$usuario->email}.");
        }

        if (!$usuario->ativo) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);
            return back()->withErrors(['login' => 'Usuário não encontrado ou inativo.']);
        }

        if (!Auth::guard('usuarios')->attempt(['login' => $credentials['login'], 'password' => $credentials['senha']], $request->boolean('lembrar'))) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            $remaining = self::MAX_ATTEMPTS - RateLimiter::attempts($throttleKey);

            Log::warning('Falha de login', [
                'ip'         => $request->ip(),
                'login'      => $credentials['login'],
                'tentativas' => RateLimiter::attempts($throttleKey),
            ]);

            $aviso = $remaining > 0
                ? " ({$remaining} tentativa(s) restante(s) antes do bloqueio)"
                : '';

            return back()->withErrors(['login' => 'Login ou senha incorretos.' . $aviso]);
        }

        RateLimiter::clear($throttleKey);

        $usuario->update(['ultimo_acesso' => now()]);
        $usuario->registrarAuditoria('Login', null, null, null, null);

        if ($usuario->tenant_id) {
            \App\Services\OnboardingService::marcar($usuario->tenant_id, 'primeiro_acesso');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function trocarSenha(Request $request)
    {
        $request->validate([
            'senha_atual'   => 'required',
            'nova_senha'    => 'required|min:8|confirmed',
            'nova_senha_confirmation' => 'required',
        ], [
            'senha_atual.required'  => 'Informe a senha atual.',
            'nova_senha.required'   => 'Informe a nova senha.',
            'nova_senha.min'        => 'A nova senha deve ter ao menos 8 caracteres.',
            'nova_senha.confirmed'  => 'As senhas não coincidem.',
        ]);

        $usuario = Auth::guard('usuarios')->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->senha_atual, $usuario->password)) {
            return back()->with('erro', 'Senha atual incorreta.');
        }

        $usuario->update(['password' => \Illuminate\Support\Facades\Hash::make($request->nova_senha)]);

        return back()->with('sucesso', 'Senha alterada com sucesso!');
    }

    public function salvarPerfil(Request $request)
    {
        $request->validate([
            'nome'  => 'nullable|string|max:150',
            'cargo' => 'nullable|string|max:100',
        ]);

        Auth::guard('usuarios')->user()->update([
            'nome'  => $request->nome  ?: null,
            'cargo' => $request->cargo ?: null,
        ]);

        return back()->with('sucesso', 'Perfil atualizado com sucesso!');
    }

    public function logout(Request $request)
    {
        Auth::guard('usuarios')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function throttleKey(Request $request): string
    {
        return 'login:' . Str::lower($request->input('login', '')) . '|' . $request->ip();
    }
}
