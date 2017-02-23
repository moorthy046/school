<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \App\Http\Middleware\VerifyInstallation::class,
            \App\Http\Middleware\VerifyUpdate::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\Locale::class,
        ],

        'api' => [
            'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin' => \App\Http\Middleware\Admin::class,
        'teacher' => \App\Http\Middleware\Teacher::class,
        'student' => \App\Http\Middleware\Student::class,
        'parent' => \App\Http\Middleware\Parents::class,
        'librarian' => \App\Http\Middleware\Librarian::class,
        'sentinel' => \App\Http\Middleware\SentinelAuth::class,
        'has_any_role' => \App\Http\Middleware\HasAnyRole::class,
        'jwt' => \Dingo\Api\Auth\Provider\JWT::class,
        'api.teacher' => \App\Http\Middleware\ApiTeacher::class,
        'api.student' => \App\Http\Middleware\ApiStudent::class,
        'api.parent' => \App\Http\Middleware\ApiParents::class,
        'api.librarian' => \App\Http\Middleware\ApiLibrarian::class,
        'xss_protection' => \App\Http\Middleware\XSSProtection::class
    ];
}
