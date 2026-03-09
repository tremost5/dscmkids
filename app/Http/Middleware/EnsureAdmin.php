<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('admin.login');
        }

        if (!$request->user()->isAdmin()) {
            abort(403, 'Akses hanya untuk admin.');
        }

        if (!$request->user()->is_active) {
            abort(403, 'Akun admin ini sudah dinonaktifkan.');
        }

        return $next($request);
    }
}
