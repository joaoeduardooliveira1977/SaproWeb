<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class AuthController extends Controller
{
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

        $usuario = Usuario::where('login', $credentials['login'])->first();

        if (!$usuario || !$usuario->ativo) {
            return back()->withErrors(['login' => 'Usuário não encontrado ou inativo.']);
        }

        if (!Auth::guard('usuarios')->attempt(['login' => $credentials['login'], 'password' => $credentials['senha']], $request->boolean('lembrar'))) {
            return back()->withErrors(['login' => 'Login ou senha incorretos.']);
        }

        $usuario->update(['ultimo_acesso' => now()]);
        $usuario->registrarAuditoria('Login', null, null, null, null);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('usuarios')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
