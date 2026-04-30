<?php

namespace App\Livewire\Freelancer;

use App\Models\Referral;
use App\Models\WalletLog;
use App\Services\FeeService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AffiliatePanel extends Component
{
    public string $affiliateCode = '';
    public string $affiliateLink = '';
    public float $saldoDisponivel = 0;
    public int $totalAfiliados = 0;
    public float $comissaoPorAfiliado = 200;
    public $history = [];
    public $referrals = [];

    public function mount()
    {
        $user = Auth::user();
        $this->affiliateCode = (string) ($user->affiliate_code ?? '');
        $this->affiliateLink = $this->affiliateCode
            ? url('/register?ref=' . $this->affiliateCode)
            : '';

        $this->comissaoPorAfiliado = FeeService::affiliateSignupCommission();

        $this->totalAfiliados = Referral::where('affiliate_id', $user->id)->count();
        $this->saldoDisponivel = (float) WalletLog::where('user_id', $user->id)
            ->where('tipo', 'comissao_afiliado')
            ->sum('valor');

        $logs = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'comissao_afiliado')
            ->orderByDesc('created_at')
            ->get();

        $this->history = $logs->map(function ($log) {
            return [
                'created_at' => $log->created_at,
                'amount'     => $log->valor,
                'description' => $log->descricao ?? 'Comissão de afiliado',
            ];
        })->toArray();

        $this->referrals = Referral::with('user')
            ->where('affiliate_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                'name'       => $r->user->name ?? 'Utilizador',
                'email'      => $r->user->email ?? '—',
                'created_at' => $r->created_at,
            ])->toArray();
    }

    public function render()
    {
        return view('livewire.freelancer.affiliate-panel')
            ->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
