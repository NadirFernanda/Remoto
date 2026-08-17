<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use App\Models\CreatorSubscription;
use App\Models\CreatorProfile;
use App\Services\SubscriptionWithdrawalGate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class SubscriptionManager extends Component
{
    public int $selectedYear;

    // ─── Preço da assinatura ───────────────────────────────────────
    public $precoAssinatura;
    public string $precoMsg     = '';
    public string $precoMsgType = 'success';

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $profile = CreatorProfile::where('user_id', Auth::id())->first();
        $this->precoAssinatura = $profile->subscription_price ?? CreatorProfile::MIN_SUBSCRIPTION_PRICE;
    }

    public function atualizarPreco(): void
    {
        $this->validate([
            'precoAssinatura' => ['required', 'numeric', 'min:' . CreatorProfile::MIN_SUBSCRIPTION_PRICE],
        ], [
            'precoAssinatura.min' => 'O valor mínimo permitido pela plataforma é Kz ' . number_format(CreatorProfile::MIN_SUBSCRIPTION_PRICE, 0, ',', '.') . '.',
        ]);

        CreatorProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            ['subscription_price' => $this->precoAssinatura]
        );

        $this->precoMsgType = 'success';
        $this->precoMsg     = 'Preço da assinatura atualizado! Só se aplica a novas assinaturas — quem já é assinante mantém o valor pago até renovar.';
    }

    public function render()
    {
        $user = Auth::user();

        if (!$user->has_creator_profile) {
            return redirect()->route('creator.activate');
        }

        $creatorProfile = CreatorProfile::where('user_id', $user->id)->first();

        // ── Summary cards ─────────────────────────────────────────────────────
        $activeSubscribers = CreatorSubscription::where('creator_id', $user->id)
            ->active()
            ->count();

        // MRR = active subscriptions net amount
        $mrr = CreatorSubscription::where('creator_id', $user->id)
            ->active()
            ->sum('net_amount');

        $allTimeEarnings = CreatorSubscription::where('creator_id', $user->id)
            ->sum('net_amount');

        $totalSubscriptions = CreatorSubscription::where('creator_id', $user->id)->count();

        // Saldo Disponível = total líquido recebido de todas as assinaturas
        $saldoDisponivel = $allTimeEarnings;

        // Valor da assinatura mensal
        $valorAssinatura = $creatorProfile->subscription_price ?? CreatorProfile::MIN_SUBSCRIPTION_PRICE;

        // Comissão da plataforma sobre o preço actual da assinatura (25%) —
        // reflecte sempre o preço em vigor agora, não o histórico de pagamentos
        // já feitos (que pode ter sido a um preço antigo).
        $comissaoTotal = (new \App\Services\FeeService())->calculateSubscriptionFee($valorAssinatura)['comissao'];

        // ── Monthly new subscriptions for selected year ──────────────────────
        $monthlyNew = CreatorSubscription::where('creator_id', $user->id)
            ->whereYear('starts_at', $this->selectedYear)
            ->selectRaw("EXTRACT(MONTH FROM starts_at)::int as month_num, COUNT(*) as new_count, SUM(net_amount) as revenue")
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get()
            ->keyBy('month_num');

        // ── Monthly cancellations for selected year ──────────────────────────
        $monthlyCancelled = CreatorSubscription::where('creator_id', $user->id)
            ->whereNotNull('cancelled_at')
            ->whereYear('cancelled_at', $this->selectedYear)
            ->selectRaw("EXTRACT(MONTH FROM cancelled_at)::int as month_num, COUNT(*) as cancelled_count")
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get()
            ->keyBy('month_num');

        // ── Build 12-month grid ──────────────────────────────────────────────
        $ptMonths = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $newCount     = (int) ($monthlyNew->get($m)?->new_count ?? 0);
            $cancelled    = (int) ($monthlyCancelled->get($m)?->cancelled_count ?? 0);
            $revenue      = (float) ($monthlyNew->get($m)?->revenue ?? 0);
            $months[$m]   = [
                'label'     => $ptMonths[$m - 1],
                'new'       => $newCount,
                'cancelled' => $cancelled,
                'net'       => $newCount - $cancelled,
                'revenue'   => $revenue,
            ];
        }

        $maxNew = max(array_column($months, 'new')) ?: 1;

        // ── Recent active subscribers ────────────────────────────────────────
        $recentSubscribers = CreatorSubscription::where('creator_id', $user->id)
            ->active()
            ->with('subscriber')
            ->latest('starts_at')
            ->take(10)
            ->get();

        // ── Available years (for year selector) ──────────────────────────────
        $minStartsAt = CreatorSubscription::where('creator_id', $user->id)->min('starts_at');
        $firstYear = $minStartsAt ? Carbon::parse($minStartsAt)->year : now()->year;
        $years = range(now()->year, $firstYear, -1);

        $saldoAssinAtribuivel      = SubscriptionWithdrawalGate::saldoAtribuivel($user->id);
        $gatedPorAssinaturas       = $saldoAssinAtribuivel > 0;
        $diasParaProximoSaqueAssin = $gatedPorAssinaturas ? SubscriptionWithdrawalGate::diasParaProximoSaque($user->id) : 0;

        return view('livewire.creator.subscription-manager', compact(
            'user',
            'creatorProfile',
            'activeSubscribers',
            'mrr',
            'allTimeEarnings',
            'totalSubscriptions',
            'saldoDisponivel',
            'comissaoTotal',
            'valorAssinatura',
            'months',
            'maxNew',
            'recentSubscribers',
            'years',
            'saldoAssinAtribuivel',
            'gatedPorAssinaturas',
            'diasParaProximoSaqueAssin',
        ))->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }

}
