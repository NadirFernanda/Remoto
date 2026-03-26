<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use App\Models\CreatorSubscription;
use App\Models\CreatorProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class SubscriptionManager extends Component
{
    public int $selectedYear;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
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

        // Comissão total retida pela plataforma (25%)
        $comissaoTotal = CreatorSubscription::where('creator_id', $user->id)
            ->sum('platform_fee');

        // Valor da assinatura mensal
        $valorAssinatura = \App\Models\CreatorProfile::SUBSCRIPTION_PRICE;

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
        $firstYear = (int) (CreatorSubscription::where('creator_id', $user->id)
            ->min('starts_at') ?? now());
        $firstYear = $firstYear ?: now()->year;
        if (!is_int($firstYear)) {
            $firstYear = Carbon::parse($firstYear)->year;
        }
        $years = range(now()->year, $firstYear, -1);

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
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão de Assinaturas']);
    }
}
