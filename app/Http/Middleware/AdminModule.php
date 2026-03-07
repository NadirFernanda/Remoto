<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controla o acesso aos módulos do painel administrativo.
 *
 * Utilização nas rotas: ->middleware('admin.module:financeiro')
 *
 * Módulos disponíveis:
 *   gestor     → master, gestor
 *   financeiro → master, financeiro
 *   settings   → master
 *   audit      → master, gestor
 */
class AdminModule
{
    // Mapa de módulo → admin_roles com acesso (null = master implícito)
    private const ACCESS = [
        'gestor'     => ['master', 'gestor', null],
        'financeiro' => ['master', 'financeiro', null],
        'settings'   => ['master', null],
        'audit'      => ['master', 'gestor', null],
        'suporte'    => ['master', 'gestor', null],
    ];

    public function handle(Request $request, Closure $next, string $module = 'gestor')
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }

        $allowed = self::ACCESS[$module] ?? ['master', null];

        if (!in_array($user->admin_role, $allowed, true)) {
            abort(403, 'O seu perfil de administrador não tem acesso a este módulo.');
        }

        return $next($request);
    }
}
