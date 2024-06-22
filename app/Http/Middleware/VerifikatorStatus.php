<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifikatorStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->status !== 'verifikator') {
            return response()->json([
                "status" => false,
                "message" => "Akses terlarang. Akses hanya diizinkan untuk verifikator"
            ], 403);
        }

        return $next($request);
    }
}
