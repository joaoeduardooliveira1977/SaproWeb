<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('usuarios')->check()) {
            return redirect()->route('master.login');
        }

        if (Auth::guard('usuarios')->user()->perfil !== 'super_admin') {
            Auth::guard('usuarios')->logout();
            return redirect()->route('master.login')
                ->with('error', 'Acesso negado. Esta área é exclusiva para administradores do sistema.');
        }

        return $next($request);
    }
}
