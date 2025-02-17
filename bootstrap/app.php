<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


// Sets up routing, middleware, and exception handling.
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // Appends middleware to handle Inertia.js requests and preload assets for performance.
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //Creates shortcuts for Spatie role-based access control middleware.
    $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class, //'role': Ensures the user has a specific role.
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class, //'permission': Ensures the user has a specific permission.
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class, //'role_or_permission': Ensures the user has either a role or a permission.
        ]);

    // Disables CSRF protection for routes starting with "stripe/"
    // This is required for Stripe webhooks to work correctly.
    $middleware->validateCsrfTokens(except:[
        'stripe/webhook'
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
