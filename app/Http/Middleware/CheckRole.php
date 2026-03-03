<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'error' => 'Пользователь не авторизован'
            ], 401);
        }

        $roles = explode('|', $role);

        if (!in_array($user->role->value, $roles)) {
            return response()->json([
                'error' => 'Доступ запрещен'
            ], 403);
        }

        return $next($request);
    }
}
