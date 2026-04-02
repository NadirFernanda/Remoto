<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Captura e persiste o código de afiliado da URL (?ref=XXXX) num cookie de 30 dias.
 * Desta forma, mesmo que o utilizador navegue antes de se registar, o referral é mantido.
 */
class CaptureAffiliateRef
{
    public function handle(Request $request, Closure $next): Response
    {
        $ref = $request->query('ref');

        if ($ref && preg_match('/^[A-Za-z0-9]{4,16}$/', $ref)) {
            $response = $next($request);

            // Apenas guarda se não tiver já um cookie (first-touch attribution)
            if (!$request->cookie('affiliate_ref')) {
                $response->cookie('affiliate_ref', strtoupper($ref), 60 * 24 * 30); // 30 dias
            }

            return $response;
        }

        return $next($request);
    }
}
