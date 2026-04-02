<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // ...existing code...
    ];

    protected $middlewareGroups = [
        'web' => [
            // ...existing code...
            \App\Http\Middleware\CaptureAffiliateRef::class,
        ],
        'api' => [
            // ...existing code...
        ],
    ];

    protected $routeMiddleware = [
        // ...existing code...
        'role' => \App\Http\Middleware\Role::class,
    ];
}
