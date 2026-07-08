<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    /**
     * Gera o manifest.webmanifest dinamicamente, acrescentando um parâmetro de
     * versão (mtime do ficheiro) aos ícones e capturas de ecrã — isto força o
     * Chrome a ir buscar as imagens novas sempre que são substituídas, em vez
     * de continuar a usar uma versão antiga guardada na sua cache interna de
     * instalabilidade (que não é limpa por um simples recarregamento da página).
     */
    public function show(): JsonResponse
    {
        $v = fn (string $path) => asset($path) . '?v=' . filemtime(public_path($path));

        $manifest = [
            'id' => '/?pwa=1',
            'name' => '24 Horas',
            'short_name' => '24 Horas',
            'description' => 'A plataforma mais rápida para conectar clientes e freelancers.',
            'start_url' => '/?pwa=1',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#080d1a',
            'theme_color' => '#080d1a',
            'orientation' => 'portrait-primary',
            'icons' => [
                ['src' => $v('img/pwa/icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => $v('img/pwa/icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => $v('img/pwa/icon-192-maskable.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'maskable'],
                ['src' => $v('img/pwa/icon-512-maskable.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
            'screenshots' => [
                ['src' => $v('img/pwa/screenshot-wide.png'), 'sizes' => '1280x800', 'type' => 'image/png', 'form_factor' => 'wide', 'label' => 'Página inicial no computador'],
                ['src' => $v('img/pwa/screenshot-mobile.png'), 'sizes' => '390x844', 'type' => 'image/png', 'label' => 'Página inicial no telemóvel'],
            ],
        ];

        return response()->json($manifest, 200, ['Content-Type' => 'application/manifest+json'])
            ->setEtag(md5(json_encode($manifest)))
            ->setMaxAge(300);
    }
}
