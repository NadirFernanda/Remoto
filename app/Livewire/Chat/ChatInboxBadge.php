<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\ChatRead;
use App\Models\Message;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ChatInboxBadge extends Component
{
    public function render()
    {
        $unread = 0;
        if (Auth::check()) {
            $user = Auth::user();

            // Todos os service_ids em que o utilizador participa
            $serviceIds = Service::where('cliente_id', $user->id)
                ->orWhere('freelancer_id', $user->id)
                ->pluck('id');

            foreach ($serviceIds as $serviceId) {
                $unread += ChatRead::unreadCount($serviceId, $user->id);
            }
        }

        return view('livewire.chat.chat-inbox-badge', ['unread' => $unread]);
    }
}
