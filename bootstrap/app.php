<?php

use App\Exceptions\BusinessException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            BusinessException $exception,
            Request $request
        ) {
            if (! $request->expectsJson()) {
                return null;
            }

            $response = [
                'success' => false,
                'message' => $exception->getMessage(),
            ];

            if ($exception->errors !== []) {
                $response['errors'] = $exception->errors;
            }

            return response()->json($response, $exception->statusCode);
        });

        $exceptions->render(function (
            ValidationException $exception,
            Request $request
        ) {
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], 422);
        });

        $exceptions->render(function (
            AuthenticationException $exception,
            Request $request
        ) {
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        });
    })->create();
