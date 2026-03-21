<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Role
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->activeRole() !== $role) {
            // Admin routes stay hard-forbidden
            if ($role === 'admin') {
                abort(403, 'Acesso restrito a administradores.');
            }

            // For all other role mismatches, redirect with context
            session()->flash('role_redirect', $role);
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
