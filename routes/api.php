<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ProposalController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
| Authentication: Laravel Sanctum token-based.
|
| Setup checklist (run once after `composer install`):
|   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
|   php artisan migrate
|
| Obtain a token:  POST /api/auth/login  →  { token, user }
| Use in requests: Authorization: Bearer <token>
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Public ──────────────────────────────────────────────────────────────

    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('login',    [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login');
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register');
    });

    // Public service/freelancer discovery
    Route::get('services',          [ServiceController::class, 'index'])->name('services.index');
    Route::get('services/{service}', [ServiceController::class, 'show'])->name('services.show');

    // ── Authenticated ────────────────────────────────────────────────────────

    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('auth/me',      [AuthController::class, 'me'])->name('auth.me');

        // Profile
        Route::get('profile',  [ProfileController::class, 'show'])->name('profile.show');
        Route::put('profile',  [ProfileController::class, 'update'])->name('profile.update');

        // Services (owner actions)
        Route::post('services',           [ServiceController::class, 'store'])->middleware('throttle:10,1')->name('services.store');
        Route::put('services/{service}',  [ServiceController::class, 'update'])->name('services.update');

        // Proposals on a service
        Route::get('services/{service}/proposals',  [ProposalController::class, 'index'])->name('proposals.index');
        Route::post('services/{service}/proposals', [ProposalController::class, 'store'])->middleware('throttle:proposals')->name('proposals.store');
        Route::get('proposals/mine',                [ProposalController::class, 'mine'])->name('proposals.mine');

        // Notifications
        Route::get('notifications',                   [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unread-count',      [NotificationController::class, 'unreadCount'])->name('notifications.unread');
        Route::patch('notifications/{id}/read',       [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('notifications/mark-all-read',    [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    });
});
