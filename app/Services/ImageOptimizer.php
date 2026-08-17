<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Redimensiona e recodifica imagens carregadas antes de as guardar — evita
 * servir originais de câmara/telemóvel (frequentemente vários MB, milhares
 * de pixels de largura) onde só é preciso uma versão pequena/média para
 * ecrã. É a causa principal do carregamento lento de imagens reportado
 * (avatares, capas, portfólio, publicações, produtos, anexos).
 */
class ImageOptimizer
{
    /**
     * @param UploadedFile $file
     * @param string $directory Directoria dentro do disco (ex.: 'avatars')
     * @param string $disk Nome do disco Laravel (ex.: 'public', 'private')
     * @param int $maxWidth Largura máxima em pixels — a imagem nunca é ampliada
     * @param int $quality Qualidade de recodificação JPEG (0-100)
     * @return string O caminho guardado (relativo ao disco), para gravar tal
     *                 como o resultado de UploadedFile::store()
     */
    public static function store(UploadedFile $file, string $directory, string $disk, int $maxWidth = 1600, int $quality = 80): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        // GIFs (frequentemente animados) e formatos não suportados pelo GD
        // ficam como estão — evita quebrar animações ou falhar em ficheiros
        // que na prática nunca são "fotos de telemóvel" gigantes.
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return $file->store($directory, $disk);
        }

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            $image->scaleDown(width: $maxWidth);

            $filename = $directory . '/' . Str::random(40) . '.jpg';
            Storage::disk($disk)->put($filename, (string) $image->toJpeg($quality));

            return $filename;
        } catch (\Throwable $e) {
            // Nunca bloquear o upload por causa de uma falha de optimização —
            // guarda o ficheiro original tal como antes.
            Log::warning('ImageOptimizer: falha ao redimensionar, a guardar original', [
                'error' => $e->getMessage(),
            ]);

            return $file->store($directory, $disk);
        }
    }
}
