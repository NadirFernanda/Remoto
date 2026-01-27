<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Sponsorship;

class SponsorshipPanel extends Component
{
    public $status;
    public $history = [];
    public $feedback = '';

    public function mount()
    {
        $user = Auth::user();
        $sponsorship = Sponsorship::where('user_id', $user->id)->first();
        if ($sponsorship) {
            $this->status = $sponsorship->status;
            // Exemplo: histórico de patrocínios (ajustar conforme estrutura real)
            $this->history = [
                [
                    'date' => now()->subDays(3)->toDateString(),
                    'amount' => 200.00,
                    'description' => 'Patrocínio aprovado',
                ],
                [
                    'date' => now()->subDays(20)->toDateString(),
                    'amount' => 0.00,
                    'description' => 'Solicitação recusada',
                ],
            ];
        } else {
            $this->status = 'não solicitado';
        }
    }

    public function solicitarPatrocinio()
    {
        // Lógica de solicitação (exemplo)
        $this->feedback = 'Solicitação enviada para análise!';
        $this->status = 'em análise';
    }

    public function render()
    {
        return view('livewire.freelancer.sponsorship-panel');
    }
}
