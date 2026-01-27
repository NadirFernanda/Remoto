<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Affiliate;

class AffiliatePanel extends Component
{
    public $affiliateCode;
    public $earnings;
    public $status;
    public $history = [];

    public function mount()
    {
        $user = Auth::user();
        $affiliate = Affiliate::where('user_id', $user->id)->first();
        if ($affiliate) {
            $this->affiliateCode = $affiliate->codigo;
            $this->earnings = $affiliate->ganhos;
            $this->status = $affiliate->status;
            // Exemplo: histórico de ganhos (pode ser ajustado conforme estrutura real)
            $this->history = [
                [
                    'date' => now()->subDays(2)->toDateString(),
                    'amount' => 50.00,
                    'description' => 'Indicação de serviço',
                ],
                [
                    'date' => now()->subDays(10)->toDateString(),
                    'amount' => 100.00,
                    'description' => 'Indicação de freelancer',
                ],
            ];
        }
    }

    public function render()
    {
        return view('livewire.freelancer.affiliate-panel');
    }
}
