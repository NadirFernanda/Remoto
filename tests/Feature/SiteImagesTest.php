<?php

namespace Tests\Feature;

use App\Livewire\Admin\SiteImages;
use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Painel Admin > Imagens do Site — permite substituir os banners do
 * carrossel, o fundo da homepage e a imagem da tela de login via
 * interface, sem precisar de deploy/código.
 */
class SiteImagesTest extends TestCase
{
    use RefreshDatabase;

    private function makeMasterAdmin(): User
    {
        $admin = User::factory()->create([
            'role'                    => 'admin',
            'admin_role'              => null,
            'email_verified_at'       => now(),
            'status'                  => 'active',
            'two_factor_confirmed_at' => now(),
        ]);

        session(['2fa_passed_at' => now()->timestamp]);

        return $admin;
    }

    #[Test]
    public function admin_sub_role_nao_pode_aceder(): void
    {
        $admin = User::factory()->create([
            'role'                    => 'admin',
            'admin_role'              => 'suporte',
            'two_factor_confirmed_at' => now(),
        ]);
        session(['2fa_passed_at' => now()->timestamp]);

        $this->actingAs($admin)
            ->get(route('admin.site-images'))
            ->assertStatus(403);
    }

    #[Test]
    public function admin_master_pode_aceder(): void
    {
        $admin = $this->makeMasterAdmin();

        $this->actingAs($admin)
            ->get(route('admin.site-images'))
            ->assertStatus(200);
    }

    #[Test]
    public function carregar_um_banner_guarda_o_caminho_e_optimiza_a_imagem(): void
    {
        Storage::fake('public');
        $admin = $this->makeMasterAdmin();

        Livewire::actingAs($admin)
            ->test(SiteImages::class)
            ->set('banner1', UploadedFile::fake()->image('banner.jpg', 3000, 1200))
            ->assertSet('savedSlot', 'banner1');

        $path = PlatformSetting::get('site_banner_1', '');
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
        $this->assertStringStartsWith('site/', $path);
    }

    #[Test]
    public function carregar_novo_banner_substitui_e_apaga_o_ficheiro_anterior(): void
    {
        Storage::fake('public');
        $admin = $this->makeMasterAdmin();

        $component = Livewire::actingAs($admin)->test(SiteImages::class);

        $component->set('banner1', UploadedFile::fake()->image('primeiro.jpg', 2000, 1000));
        $firstPath = PlatformSetting::get('site_banner_1');

        $component->set('banner1', UploadedFile::fake()->image('segundo.jpg', 2000, 1000));
        $secondPath = PlatformSetting::get('site_banner_1');

        $this->assertNotEquals($firstPath, $secondPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);
    }

    #[Test]
    public function remover_imagem_volta_ao_padrao_e_apaga_o_ficheiro(): void
    {
        Storage::fake('public');
        $admin = $this->makeMasterAdmin();

        $component = Livewire::actingAs($admin)->test(SiteImages::class);
        $component->set('login', UploadedFile::fake()->image('login.jpg', 1200, 1600));
        $path = PlatformSetting::get('site_login_image');
        $this->assertNotEmpty($path);

        $component->call('remove', 'login')
            ->assertSet('loginPath', '');

        $this->assertSame('', PlatformSetting::get('site_login_image', ''));
        Storage::disk('public')->assertMissing($path);
    }

    #[Test]
    public function helper_devolve_imagem_padrao_quando_nao_ha_ficheiro_custom(): void
    {
        $this->assertSame('/img/default.jpg', site_image_url('site_login_image', '/img/default.jpg'));
    }

    #[Test]
    public function helper_devolve_ficheiro_custom_quando_configurado(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('site/custom-login.jpg', 'conteudo-fake');
        PlatformSetting::set('site_login_image', 'site/custom-login.jpg');

        $url = site_image_url('site_login_image', '/img/default.jpg');

        $this->assertStringContainsString('site/custom-login.jpg', $url);
    }

    #[Test]
    public function pagina_de_login_usa_imagem_custom_quando_configurada(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('site/custom-login.jpg', 'conteudo-fake');
        PlatformSetting::set('site_login_image', 'site/custom-login.jpg');

        $response = $this->get('/login');

        $response->assertSee('site/custom-login.jpg', false);
    }
}
