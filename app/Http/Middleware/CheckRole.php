<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role->value, $roles)) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        return $next($request);
    }
}
