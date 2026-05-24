<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TrackAdminLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            $user = Auth::user();
            $cacheKey = 'admin_seen_' . $user->id;

            // Update at most once per minute to avoid hammering the DB
            if (!Cache::has($cacheKey)) {
                $user->timestamps = false;
                $user->last_seen_at = now();
                $user->save();
                $user->timestamps = true;

                Cache::put($cacheKey, true, 60);
            }
        }

        return $next($request);
    }
}
