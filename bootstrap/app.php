<?php

use App\Http\Middleware\JwtAuthenticate;
use App\Exceptions\ServiceException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt.auth' => JwtAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ServiceException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getStatus());
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => 'Ресурс не найден',
            ], 404);
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => 'Не авторизован',
            ], 401);
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => 'Доступ запрещен',
            ], 403);
        });

        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            $message = $e->getMessage() ?: 'Ошибка запроса';

            return response()->json([
                'message' => $message,
            ], $e->getStatusCode());
        });

        $exceptions->render(function (Throwable $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            $status = (int) $e->getCode();
            if ($status >= 400 && $status <= 599) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $status);
            }

            return response()->json([
                'message' => 'Внутренняя ошибка сервера',
            ], 500);
        });
    })->create();
