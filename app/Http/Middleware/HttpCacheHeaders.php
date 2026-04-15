<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frente 2 — Delivery Cache
 *
 * Adiciona cabeçalhos HTTP de cache às respostas:
 *   - Páginas públicas (GET sem sessão autenticada):
 *       Cache-Control: public, max-age=60, s-maxage=180, stale-while-revalidate=60
 *       → permite que CDN/proxies reversos (Nginx, Varnish, Cloudflare) guardem a resposta
 *         por 3 minutos; browsers por 1 minuto.
 *
 *   - Páginas autenticadas / mutações:
 *       Cache-Control: private, no-store
 *       → garante que nenhum proxy intermédiário armazena dados pessoais.
 *
 *   - Livewire AJAX (x-livewire: true):
 *       Cache-Control: no-store
 *       → respostas de componentes são sempre frescas.
 */
class HttpCacheHeaders
{
    // Prefixos de rota que são 100% públicos e cacheáveis
    private const PUBLIC_PREFIXES = [
        '/freelancers',
        '/projetos',
        '/projeto/',
        '/loja',
        '/social',
        '/sobre',
        '/privacidade',
        '/termos',
        '/cookies',
        '/suporte',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Não cachear se não for GET/HEAD
        if (!$request->isMethodCacheable()) {
            $response->headers->set('Cache-Control', 'no-store');
            return $response;
        }

        // Não cachear rotas internas do Livewire (upload, preview, etc.)
        if (str_starts_with($request->getPathInfo(), '/livewire')) {
            $response->headers->set('Cache-Control', 'no-store');
            return $response;
        }

        // Não cachear requisições Livewire AJAX
        if ($request->header('X-Livewire')) {
            $response->headers->set('Cache-Control', 'no-store');
            return $response;
        }

        // Não cachear se houver sessão autenticada OU cookie de sessão presente
        // (cobre o caso em que auth()->check() falha antes do middleware de sessão)
        if (auth()->check() || $request->hasCookie(config('session.cookie'))) {
            $response->headers->set('Cache-Control', 'no-store');
            return $response;
        }

        // Verificar se é uma rota pública cacheável
        if ($this->isPublicRoute($request)) {
            $response->headers->set(
                'Cache-Control',
                'public, max-age=60, s-maxage=180, stale-while-revalidate=60'
            );
            $response->headers->set('Vary', 'Accept-Encoding, Accept-Language, Cookie');
        } else {
            $response->headers->set('Cache-Control', 'no-store');
        }

        return $response;
    }

    private function isPublicRoute(Request $request): bool
    {
        $path = '/' . ltrim($request->getPathInfo(), '/');

        foreach (self::PUBLIC_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
