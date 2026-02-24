<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = $data['role'];

        $user->save();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (!$token = auth('api')->attempt($data)) {
            return response()->json(['error' => 'Неверный email или пароль'], 401);
        }

        $user = auth('api')->user();

        $user->save();

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
        ]);
    }

    public function getUser(): JsonResponse
    {
        return response()->json([
            'user' => auth('api')->user(),
        ]);
    }

    public function refresh(): JsonResponse
    {
        $token = JWTAuth::refresh();

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Вы вышли из системы'
        ]);
    }
}
