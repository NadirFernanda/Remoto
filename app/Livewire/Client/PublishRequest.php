<?php

namespace App\Livewire\Client;

use Livewire\Component;

class PublishRequest extends Component
{
    public string $need = '';

    public function submit()
    {
        $this->validate([
            'need' => 'required|min:5|max:255',
        ]);
        // Redirecionar para o briefing guiado (ajuste a rota conforme necessário)
        return redirect()->route('client.briefing', ['need' => $this->need]);
    }

    public function render()
    {
        return view('livewire.client.publish-request');
    }
}
