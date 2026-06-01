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

        if (!$usuario || !$usuario->ativo) {
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
