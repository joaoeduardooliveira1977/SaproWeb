<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class MasterLoginController extends Controller
{
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
            return back()->withErrors(['login' => 'Usuário não encontrado ou inativo.'])->withInput($request->only('login'));
        }

        if ($usuario->perfil !== 'super_admin') {
            return back()->withErrors(['login' => 'Acesso negado. Esta área é exclusiva para administradores do sistema.'])->withInput($request->only('login'));
        }

        if (!Auth::guard('usuarios')->attempt(['login' => $usuario->login, 'password' => $credentials['senha']])) {
            return back()->withErrors(['login' => 'Senha incorreta.'])->withInput($request->only('login'));
        }

        $request->session()->regenerate();
        $usuario->update(['ultimo_acesso' => now()]);

        return redirect()->intended(route('master.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('usuarios')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('master.login');
    }
}
