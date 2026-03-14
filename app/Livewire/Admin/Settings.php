<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PlatformSetting;

class Settings extends Component
{
    // General platform settings stored in platform_settings table
    public string $siteName        = '';
    public string $siteEmail       = '';
    public string $maintenanceMode = '0';
    public string $savedMsg        = '';

    public function mount(): void
    {
        $this->siteName        = PlatformSetting::get('site_name', config('app.name', ''));
        $this->siteEmail       = PlatformSetting::get('site_email', config('mail.from.address', ''));
        $this->maintenanceMode = PlatformSetting::get('maintenance_mode', '0');
    }

    public function save(): void
    {
        $this->validate([
            'siteName'        => 'required|string|max:100',
            'siteEmail'       => 'required|email|max:150',
            'maintenanceMode' => 'required|in:0,1',
        ]);

        PlatformSetting::set('site_name', $this->siteName);
        PlatformSetting::set('site_email', $this->siteEmail);
        PlatformSetting::set('maintenance_mode', $this->maintenanceMode);

        // Activar/desactivar modo de manutenção via artisan down/up
        if ($this->maintenanceMode === '1') {
            if (!file_exists(base_path('storage/framework/down'))) {
                \Artisan::call('down');
            }
        } else {
            if (file_exists(base_path('storage/framework/down'))) {
                \Artisan::call('up');
            }
        }

        $this->savedMsg = 'Configurações guardadas com sucesso.';
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Configurações Gerais']);
    }
}
