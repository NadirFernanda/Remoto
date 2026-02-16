<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\ChatRead;
use Illuminate\Support\Facades\Auth;

class ChatBadge extends Component
{
    public int $serviceId;

    public function mount(int $serviceId)
    {
        $this->serviceId = $serviceId;
    }

    public function render()
    {
        $unread = 0;
        if (Auth::check()) {
            $unread = ChatRead::unreadCount($this->serviceId, Auth::id());
        }
        return view('livewire.chat.chat-badge', ['unread' => $unread]);
    }
}
