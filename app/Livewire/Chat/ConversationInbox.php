<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Service;
use App\Models\ChatRead;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ConversationInbox extends Component
{
    public string $search = '';

    public function updatingSearch(): void
    {
        // reactive: no explicit action needed
    }

    public function render()
    {
        $user = Auth::user();

        // Todos os serviços em que o utilizador participa como cliente ou freelancer
        $query = Service::where(function ($q) use ($user) {
                $q->where('cliente_id', $user->id)
                  ->orWhere('freelancer_id', $user->id);
            })
            ->whereHas('messages')
            ->with(['cliente', 'freelancer', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }]);

        if ($this->search) {
            $query->where('titulo', 'ilike', '%' . $this->search . '%');
        }

        $services = $query->orderByDesc(
            Message::select('created_at')
                ->whereColumn('service_id', 'services.id')
                ->latest()
                ->limit(1)
        )->get();

        // Calcular mensagens não lidas por conversa
        $unreadCounts = [];
        foreach ($services as $service) {
            $unreadCounts[$service->id] = ChatRead::unreadCount($service->id, $user->id);
        }

        $totalUnread = array_sum($unreadCounts);

        return view('livewire.chat.conversation-inbox', [
            'services'     => $services,
            'unreadCounts' => $unreadCounts,
            'totalUnread'  => $totalUnread,
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Mensagens']);
    }
}
