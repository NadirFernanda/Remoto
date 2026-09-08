<?php

namespace App\Providers;

use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Repositories\Eloquent\AuditLogRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\WalletRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Features\SupportFileUploads\FileUploadController;
use Livewire\Mechanisms\FrontendAssets\FrontendAssets;
use Livewire\Mechanisms\HandleRequests\HandleRequests;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(AuditLogRepositoryInterface::class, AuditLogRepository::class);
    }

    public function boot(): void
    {
        // Keep Livewire endpoints outside the default /livewire-* prefix.
        // Some production Nginx rules treat that prefix as a static path and
        // return 404 before the request reaches Laravel.
        app(FrontendAssets::class)->setScriptRoute(function ($handle) {
            return Route::get('/lw-assets/livewire.min.js', $handle)
                ->middleware('web')
                ->name('custom.livewire.script');
        });

        app(HandleRequests::class)->setUpdateRoute(function ($handle) {
            return Route::post('/lw-update', $handle)
                ->middleware('web')
                ->name('custom.livewire.update');
        });

        Route::post('/lw-upload', [FileUploadController::class, 'handle'])
            ->middleware('web')
            ->name('livewire.upload-file');

        // ── API throttle ──────────────────────────────────────────────────────
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // ── Proposals: 5 per 10 minutes per user ─────────────────────────────
        RateLimiter::for('proposals', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->user()?->id ?: $request->ip());
        });

        // ── Reviews: 3 per hour per user ─────────────────────────────────────
        RateLimiter::for('reviews', function (Request $request) {
            return Limit::perHour(3)->by($request->user()?->id ?: $request->ip());
        });

        // ── Reports: 10 per hour per user ────────────────────────────────────
        RateLimiter::for('reports', function (Request $request) {
            return Limit::perHour(10)->by($request->user()?->id ?: $request->ip());
        });

        // ── Chat messages: 30 per minute per user ────────────────────────────
        RateLimiter::for('chat', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });
    }
}
