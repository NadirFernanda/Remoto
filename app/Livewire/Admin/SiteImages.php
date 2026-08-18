<?php

namespace App\Livewire\Admin;

use App\Models\PlatformSetting;
use App\Modules\Admin\Services\AuditLogger;
use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SiteImages extends Component
{
    use WithFileUploads;

    /** @var array<string,array{setting:string,label:string,maxWidth:int}> */
    private const SLOTS = [
        'banner1'    => ['setting' => 'site_banner_1',        'label' => 'Banner 1 do carrossel', 'maxWidth' => 1920],
        'banner2'    => ['setting' => 'site_banner_2',        'label' => 'Banner 2 do carrossel', 'maxWidth' => 1920],
        'banner3'    => ['setting' => 'site_banner_3',        'label' => 'Banner 3 do carrossel', 'maxWidth' => 1920],
        'background' => ['setting' => 'site_background_image', 'label' => 'Imagem de fundo (secção Comunidade)', 'maxWidth' => 1920],
        'login'      => ['setting' => 'site_login_image',      'label' => 'Imagem da tela de login', 'maxWidth' => 1200],
    ];

    public $banner1;
    public $banner2;
    public $banner3;
    public $background;
    public $login;

    public string $banner1Path = '';
    public string $banner2Path = '';
    public string $banner3Path = '';
    public string $backgroundPath = '';
    public string $loginPath = '';

    public string $savedSlot = '';

    public function mount(): void
    {
        abort_if(
            auth()->user()?->role !== 'admin' || auth()->user()->admin_role !== null,
            403,
            'Apenas o Admin Master pode editar as imagens do site.'
        );

        foreach (self::SLOTS as $property => $slot) {
            $this->{$property . 'Path'} = PlatformSetting::get($slot['setting'], '');
        }
    }

    public function updatedBanner1(): void
    {
        $this->storeSlot('banner1');
    }

    public function updatedBanner2(): void
    {
        $this->storeSlot('banner2');
    }

    public function updatedBanner3(): void
    {
        $this->storeSlot('banner3');
    }

    public function updatedBackground(): void
    {
        $this->storeSlot('background');
    }

    public function updatedLogin(): void
    {
        $this->storeSlot('login');
    }

    private function storeSlot(string $property): void
    {
        $slot = self::SLOTS[$property];

        $this->validate([$property => 'image|max:5120'], [
            "{$property}.image" => 'O ficheiro deve ser uma imagem (JPG, PNG ou WEBP).',
            "{$property}.max"   => 'A imagem não pode exceder 5 MB.',
        ]);

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $this->{$property};
        $old  = PlatformSetting::get($slot['setting'], '');

        $path = ImageOptimizer::store($file, 'site', 'public', $slot['maxWidth']);
        PlatformSetting::set($slot['setting'], $path);

        if ($old && str_starts_with($old, 'site/') && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        AuditLogger::log(
            'site_image_updated',
            "Imagem do site actualizada: {$slot['label']}",
            'PlatformSetting',
            null,
            category: 'sistema'
        );

        $this->{$property . 'Path'} = $path;
        $this->{$property} = null;
        $this->savedSlot = $property;
    }

    public function remove(string $property): void
    {
        if (!isset(self::SLOTS[$property])) {
            return;
        }

        $slot = self::SLOTS[$property];
        $old  = PlatformSetting::get($slot['setting'], '');

        if ($old && str_starts_with($old, 'site/') && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        PlatformSetting::set($slot['setting'], '');

        AuditLogger::log(
            'site_image_removed',
            "Imagem do site removida (volta ao padrão): {$slot['label']}",
            'PlatformSetting',
            null,
            category: 'sistema'
        );

        $this->{$property . 'Path'} = '';
        $this->savedSlot = '';
    }

    public function render()
    {
        return view('livewire.admin.site-images')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Imagens do Site']);
    }
}
