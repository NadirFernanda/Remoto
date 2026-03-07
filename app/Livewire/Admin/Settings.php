<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class Settings extends Component
{
    // General platform settings stored in DB/config cache
    public string $siteName        = '';
    public string $siteEmail       = '';
    public string $maintenanceMode = '0';
    public string $savedMsg        = '';

    public function mount(): void
    {
        $this->siteName        = config('app.name', '');
        $this->siteEmail       = config('mail.from.address', '');
        $this->maintenanceMode = file_exists(base_path('storage/framework/down')) ? '1' : '0';
    }

    public function save(): void
    {
        // Persist settings to .env or a settings table (placeholder)
        $this->savedMsg = 'Configurações guardadas com sucesso.';
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Configurações Gerais']);
    }
}
