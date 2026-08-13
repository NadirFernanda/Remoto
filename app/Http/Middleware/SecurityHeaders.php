<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cabeçalhos de segurança de defesa em profundidade, identificados na
 * auditoria de 13/08/2026 (F2, F3 — HSTS/Referrer-Policy/Permissions-Policy/CSP).
 *
 * A CSP começa em modo Report-Only (F3, fase 3) — mapeada a partir das
 * origens reais usadas pelo site (Google Fonts, jsDelivr para o emoji
 * picker do chat, Pusher para o realtime, Unsplash para imagens de
 * demonstração), não bloqueia nada ainda. As violações são reportadas
 * para /api/csp-report e ficam no log, para confirmarmos que a política
 * está completa antes de a tornar bloqueante.
 *
 * Nota: Livewire corre com 'csp_safe' => false (config/livewire.php), por
 * isso script-src precisa de 'unsafe-eval' — passar para o build CSP-safe
 * do Livewire é um passo à parte, maior, fora do âmbito desta fase.
 */
class SecurityHeaders
{
    private const CSP_REPORT_ONLY = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-eval' https://cdn.jsdelivr.net",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
        "font-src 'self' https://fonts.gstatic.com",
        "img-src 'self' data: blob: https://images.unsplash.com",
        "connect-src 'self' wss://*.pusher.com https://*.pusher.com",
        "frame-ancestors 'self'",
        "base-uri 'self'",
        "form-action 'self'",
        "object-src 'none'",
        "report-uri /api/csp-report",
    ];

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
        $response->headers->set('Content-Security-Policy-Report-Only', implode('; ', self::CSP_REPORT_ONLY));

        return $response;
    }
}
