<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Service;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Calcular mensagens não lidas com 1 query JOIN em vez de N×2 queries
        $serviceIds = $services->pluck('id')->all();
        $unreadCounts = [];
        if (!empty($serviceIds)) {
            $placeholders = implode(',', array_fill(0, count($serviceIds), '?'));
            $rows = DB::select(
                "SELECT m.service_id, COUNT(m.id) as unread
                 FROM messages m
                 LEFT JOIN chat_reads cr ON cr.service_id = m.service_id AND cr.user_id = ?
                 WHERE m.service_id IN ({$placeholders})
                   AND m.user_id != ?
                   AND (cr.last_read_at IS NULL OR m.created_at > cr.last_read_at)
                 GROUP BY m.service_id",
                array_merge([$user->id], $serviceIds, [$user->id])
            );
            foreach ($rows as $row) {
                $unreadCounts[$row->service_id] = (int) $row->unread;
            }
            foreach ($serviceIds as $sid) {
                $unreadCounts[$sid] = $unreadCounts[$sid] ?? 0;
            }
        }

        $totalUnread = array_sum($unreadCounts);

        return view('livewire.chat.conversation-inbox', [
            'services'     => $services,
            'unreadCounts' => $unreadCounts,
            'totalUnread'  => $totalUnread,
        ])->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
