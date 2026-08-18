<?php

if (!function_exists('site_image_url')) {
    /**
     * URL de uma imagem do site editável pelo admin em Configurações >
     * Imagens do Site (banners do carrossel, fundo da homepage, imagem da
     * tela de login). Devolve o ficheiro carregado pelo admin quando existe;
     * caso contrário, cai para o ficheiro estático original.
     */
    function site_image_url(string $settingKey, string $defaultAssetPath): string
    {
        $custom = \App\Models\PlatformSetting::get($settingKey, '');

        if ($custom && \Illuminate\Support\Facades\Storage::disk('public')->exists($custom)) {
            return \Illuminate\Support\Facades\Storage::url($custom);
        }

        return $defaultAssetPath;
    }
}
