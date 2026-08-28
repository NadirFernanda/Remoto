<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Bloqueia o acesso de utilizadores suspensos — antes desta middleware,
 * marcar "is_suspended" na base de dados não tinha nenhum efeito real:
 * o utilizador continuava a conseguir usar a plataforma normalmente.
 */
class EnsureNotSuspended
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->is_suspended) {
            // Suspensão automática (por advertências) já expirou — reactiva
            // agora mesmo, sem esperar pela tarefa agendada diária.
            if ($user->suspended_until && $user->suspended_until->isPast()) {
                $user->is_suspended    = false;
                $user->status          = 'active';
                $user->suspended_until = null;
                $user->save();
            } else {
                $message = $user->suspended_until
                    ? 'A sua conta está temporariamente suspensa até ' . $user->suspended_until->format('d/m/Y') . ' devido a advertências por incumprimento das suas responsabilidades.'
                    : 'A sua conta foi suspensa. Contacte o suporte para mais informações.';

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', $message);
            }
        }

        return $next($request);
    }
}
