<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\User\User;
use App\Services\Role\RoleAssignerService;
use Illuminate\Support\Str;

class JwtAuthenticate
{
    public function __construct(private RoleAssignerService $roleAssigner)
    {
    }

    public function handle($request, Closure $next)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();

            $user = User::firstOrCreate(
                ['email' => $payload->get('email')],
                ['name' => $payload->get('name') ?? 'User', 'password' => bcrypt(Str::random(16))]
            );

            $this->roleAssigner->assign($user);
            $user->save();

            Auth::login($user);

        } catch (Exception $e) {
            return response()->json(['error' => 'Неверный или истёкший токен'], 401);
        }

        return $next($request);
    }
}
