<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->perfil !== 'super_admin') {
            abort(403, 'Acesso restrito.');
        }
        return $next($request);
    }
}
