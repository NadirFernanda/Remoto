<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Settings extends Component
{
    public $user;
    public $notify_new_project_email;

    public function mount()
    {
        $this->user = Auth::user();
        $this->notify_new_project_email = $this->user->notify_new_project_email;
    }

    public function updatedNotifyNewProjectEmail($value)
    {
        $this->user->notify_new_project_email = $value;
        $this->user->save();
        session()->flash('success', 'Preferência de notificação atualizada com sucesso!');
    }

    public function render()
    {
        return view('livewire.client.settings')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Configurações']);
    }
}
