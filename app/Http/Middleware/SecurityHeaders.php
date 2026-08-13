<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cabeçalhos de segurança de defesa em profundidade, identificados na
 * auditoria de 13/08/2026 (F2, F3 — HSTS/Referrer-Policy/Permissions-Policy).
 *
 * Content-Security-Policy fica de fora deliberadamente — precisa de um
 * mapeamento cuidadoso em modo Report-Only antes de aplicar em modo
 * bloqueante, para não quebrar fontes/assets existentes.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->secure()) {
            // Sem "includeSubDomains" por agora — o certificado é wildcard
            // (*.24horas.ao), mas não confirmámos que todos os subdomínios em
            // uso servem HTTPS correctamente. Adicionar depois de confirmar.
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=()');

        return $response;
    }
}
