<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = auth()->user();

        if ($user->role->value != $role) {
            return response()->json([
                'error' => 'Доступ запрещен'
            ], 403);
        }

        return $next($request);
    }
}
