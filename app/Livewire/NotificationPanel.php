<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationPanel extends Component
{
    public $notifications = [];

    public function mount()
    {
        $user = Auth::user();
        // Exemplo: notificações mockadas (substituir por busca real se necessário)
        $this->notifications = [
            [
                'type' => 'info',
                'message' => 'Seu serviço foi entregue com sucesso!',
                'date' => now()->subDay()->toDateTimeString(),
            ],
            [
                'type' => 'warning',
                'message' => 'Você possui um pagamento pendente.',
                'date' => now()->subDays(2)->toDateTimeString(),
            ],
            [
                'type' => 'success',
                'message' => 'Parabéns! Você recebeu uma nova avaliação.',
                'date' => now()->subDays(3)->toDateTimeString(),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.notification-panel');
    }
}
