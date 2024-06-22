<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->status !== 'admin') {
            return response()->json([
                "status" => false,
                "message" => "Akses terlarang. Akses hanya diizinkan untuk admin"
            ], 403);
        }

        return $next($request);
    }
}
