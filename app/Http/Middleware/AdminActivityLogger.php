<?php

namespace App\Http\Middleware;

use App\Models\AdminActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class AdminActivityLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            $request->user()
            && $request->user()->isAdmin()
            && $request->isMethodSafe() === false
            && Schema::hasTable('admin_activity_logs')
        ) {
            AdminActivityLog::create([
                'user_id' => $request->user()->id,
                'method' => $request->method(),
                'path' => '/'.$request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
            ]);
        }

        return $response;
    }
}

