<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\User\User;
use Illuminate\Support\Str;

class JwtAuthenticate
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            Auth::login($user);
        } catch (Exception $e) {
            return response()->json(['error' => 'Неверный или истёкший токен'], 401);
        }

        return $next($request);
    }
}
