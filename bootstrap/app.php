<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            Illuminate\Http\Middleware\HandleCors::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([ // thiss
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'cors' => Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle failed authorization (Spatie)
        $exceptions->render(function (AuthorizationException $exception, $request) {
            return response()->json([
                'message' => 'You do not have the required permissions to access this resource.',
                'status' => 'Forbidden',
            ], 403); // 403 Forbidden
        });

        // Handle failed authentication (Sanctum)
        $exceptions->render(function (Illuminate\Auth\AuthenticationException $exception, $request) {
            return response()->json([
                'message' => 'You must login to access this resource.',
                'status' => 'Unauthorized',
            ], 401); // 401 Unauthorized
        });
    })->create();
