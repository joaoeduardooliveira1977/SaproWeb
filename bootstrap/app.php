<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'perfil'            => \App\Http\Middleware\VerificarPerfil::class,
            'tenant'            => \App\Http\Middleware\IdentificarTenant::class,
            'super_admin'       => \App\Http\Middleware\SuperAdmin::class,
            'master_auth'       => \App\Http\Middleware\MasterAuth::class,
            'verificar_limites' => \App\Http\Middleware\VerificarLimitesTenant::class,
            'trial_expirado'    => \App\Http\Middleware\VerificarTrialExpirado::class,
            'modulo'            => \App\Http\Middleware\VerificarModulo::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\IdentificarTenant::class,
            \App\Http\Middleware\VerificarTrialExpirado::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
