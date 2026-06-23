<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'admin.activity' => \App\Http\Middleware\AdminActivityLogger::class,
            'permission' => \App\Http\Middleware\EnsurePermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (
            TokenMismatchException $e,
            Request $request
        ) {

            if ($request->is('admin/*')) {

                return redirect()
                    ->route('admin.login')
                    ->with('warning', 'Sesi telah berakhir. Silakan login kembali.');
            }

            return redirect()->back()->with(
                'warning',
                'Halaman telah kadaluarsa. Silakan coba lagi.'
            );
        });

    })
    ->create();