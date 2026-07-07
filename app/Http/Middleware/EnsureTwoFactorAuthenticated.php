<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return $next($request);
        }

        if ($user->two_factor_confirmed_at === null) {
            return redirect()->route('2fa.setup');
        }

        if (!session('2fa_passed_at')) {
            return redirect()->route('2fa.challenge');
        }

        return $next($request);
    }
}
