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
            // Usar registros reais de Sponsorship para histórico (se houver múltiplos)
            $records = \App\Models\Sponsorship::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->take(6)
                ->get();
            $this->history = $records->map(function($r) {
                return [
                    'created_at' => $r->created_at,
                    'amount' => null,
                    'description' => 'Plano: ' . ($r->plano ?? '—') . ' — ' . ($r->status ?? ''),
                ];
            })->toArray();
        } else {
            $this->status = 'não solicitado';
            $this->history = [];
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
