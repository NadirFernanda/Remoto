<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Categories extends Component
{
    // Placeholder — categories feature to be implemented
    public function render()
    {
        return view('livewire.admin.categories')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Categorias de Serviços']);
    }
}
