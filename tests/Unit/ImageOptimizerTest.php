<?php

namespace Tests\Unit;

use App\Services\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes para o redimensionamento/recodificação de imagens no upload —
 * causa principal do carregamento lento reportado (fotos de telemóvel de
 * vários MB servidas tal como foram enviadas, mesmo para um avatar de 40px).
 */
class ImageOptimizerTest extends TestCase
{
    #[Test]
    public function reduz_uma_imagem_maior_do_que_a_largura_maxima(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('foto.jpg', 3000, 2000);

        $path = ImageOptimizer::store($file, 'avatars', 'public', maxWidth: 500);

        Storage::disk('public')->assertExists($path);

        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $image   = $manager->read(Storage::disk('public')->path($path));

        $this->assertLessThanOrEqual(500, $image->width());
        $this->assertStringEndsWith('.jpg', $path);
    }

    #[Test]
    public function nao_amplia_uma_imagem_menor_do_que_a_largura_maxima(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('pequena.jpg', 200, 150);

        $path = ImageOptimizer::store($file, 'avatars', 'public', maxWidth: 500);

        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $image   = $manager->read(Storage::disk('public')->path($path));

        $this->assertEquals(200, $image->width());
    }

    #[Test]
    public function guarda_o_ficheiro_original_para_extensoes_nao_suportadas(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('documento.pdf', 500, 'application/pdf');

        $path = ImageOptimizer::store($file, 'portfolio/documents', 'public');

        Storage::disk('public')->assertExists($path);
        $this->assertStringEndsWith('.pdf', $path);
    }
}
