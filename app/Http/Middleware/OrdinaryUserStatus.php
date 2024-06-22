<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrdinaryUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->status !== 'ordinary_user') {
            return response()->json([
                "status" => false,
                "message" => "Akses terlarang. Akses hanya diizinkan untuk ordinary user"
            ], 403);
        }

        if (auth()->user()->verif === 0) {
            return response()->json([
                "status" => false,
                "message" => "Akses ditolak. Belum terverifikasi"
            ], 401);
        }

        return $next($request);
    }
}
