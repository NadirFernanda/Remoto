<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Service;

class ChatMessages extends Component
{
    public Service $service;

    public function mount(Service $service)
    {
        $this->service = $service;
    }

    public function render()
    {
        $messages = $this->service->messages()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return view('livewire.chat.chat-messages', ['messages' => $messages]);
    }
}
