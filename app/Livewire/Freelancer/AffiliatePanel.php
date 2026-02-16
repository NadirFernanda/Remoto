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
            // Histórico real: usar WalletLog com tipo de comissão de afiliado
            $logs = \App\Models\WalletLog::where('user_id', $user->id)
                ->where('tipo', 'comissao_afiliado')
                ->orderByDesc('created_at')
                ->take(10)
                ->get();
            $this->history = $logs->map(function($l) {
                return [
                    'created_at' => $l->created_at,
                    'amount' => $l->valor,
                    'description' => $l->descricao ?? 'Comissão de afiliado',
                ];
            })->toArray();
        }
    }

    public function render()
    {
        return view('livewire.freelancer.affiliate-panel');
    }
}
