<?php

namespace App\Livewire\Freelancer;

class Settings extends \App\Livewire\Client\Settings
{
    public function render()
    {
        return view('livewire.freelancer.settings')
            ->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
