<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use App\Models\CreatorSubscription;
use App\Models\CreatorProfile;
use App\Models\WalletLog;
use App\Models\Wallet as WalletModel;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SubscriptionManager extends Component
{
    public int $selectedYear;

    // ─── Saque das Assinaturas ───────────────────────────────────────
    public bool  $showSaqueModal     = false;
    public float $valorSaqueAssin    = 0;
    public string $saqueMsg          = '';
    public string $saqueMsgType      = 'success';

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
        $minStartsAt = CreatorSubscription::where('creator_id', $user->id)->min('starts_at');
        $firstYear = $minStartsAt ? Carbon::parse($minStartsAt)->year : now()->year;
        $years = range(now()->year, $firstYear, -1);

        // ── Saque das Assinaturas ────────────────────────────────────────────
        $totalSacadoAssin = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'assinaturas')
            ->sum(DB::raw('ABS(valor)'));
        $saldoAssinDisponivel = max(0, $allTimeEarnings - $totalSacadoAssin);

        $pendenteSaqueAssin = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'assinaturas')
            ->exists();

        // Último saque de assinaturas aprovado (tipo saque_processado ou saque_solicitado)
        $ultimoSaqueAssin = WalletLog::where('user_id', $user->id)
            ->where('fonte', 'assinaturas')
            ->whereIn('tipo', ['saque_solicitado', 'saque_processado'])
            ->latest()
            ->first();

        $diasParaProximoSaque = 0;
        $podeRealizarSaque    = true;
        if ($ultimoSaqueAssin) {
            $diasDecorridos = (int) Carbon::parse($ultimoSaqueAssin->created_at)->diffInDays(now());
            if ($diasDecorridos < 22) {
                $podeRealizarSaque    = false;
                $diasParaProximoSaque = 22 - $diasDecorridos;
            }
        }

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
            'saldoAssinDisponivel',
            'pendenteSaqueAssin',
            'podeRealizarSaque',
            'diasParaProximoSaque',
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão de Assinaturas']);
    }

    // ─── Saque das Assinaturas ───────────────────────────────────────────────

    public function abrirSaqueAssin(): void
    {
        $this->saqueMsg      = '';
        $this->saqueMsgType  = 'success';
        $this->showSaqueModal = true;
    }

    public function fecharSaqueAssin(): void
    {
        $this->showSaqueModal = false;
        $this->valorSaqueAssin = 0;
        $this->saqueMsg = '';
    }

    public function solicitarSaqueAssin(): void
    {
        $user = Auth::user();

        $allTimeEarnings = CreatorSubscription::where('creator_id', $user->id)->sum('net_amount');
        $totalSacado     = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'assinaturas')
            ->sum(DB::raw('ABS(valor)'));
        $saldoDisponivel = max(0, $allTimeEarnings - $totalSacado);

        // Verificar cooldown de 22 dias
        $ultimoSaque = WalletLog::where('user_id', $user->id)
            ->where('fonte', 'assinaturas')
            ->whereIn('tipo', ['saque_solicitado', 'saque_processado'])
            ->latest()
            ->first();

        if ($ultimoSaque) {
            $diasDecorridos = (int) Carbon::parse($ultimoSaque->created_at)->diffInDays(now());
            if ($diasDecorridos < 22) {
                $restantes = 22 - $diasDecorridos;
                $this->addError('valorSaqueAssin', "Os saques de assinaturas só podem ser feitos a cada 22 dias. Aguarde mais {$restantes} dia(s).");
                return;
            }
        }

        $minAmount = (float) PlatformSetting::get('withdrawal_min_amount', 1000);

        $this->validate([
            'valorSaqueAssin' => ['required', 'numeric', 'min:' . $minAmount],
        ], [
            'valorSaqueAssin.min' => 'O valor mínimo de saque é Kz ' . number_format($minAmount, 0, ',', '.') . '.',
        ]);

        if ($this->valorSaqueAssin > $saldoDisponivel) {
            $this->addError('valorSaqueAssin', 'Saldo insuficiente. Disponível: Kz ' . number_format($saldoDisponivel, 0, ',', '.') . '.');
            return;
        }

        $pendente = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'assinaturas')
            ->exists();
        if ($pendente) {
            $this->addError('valorSaqueAssin', 'Já tem um saque de assinaturas pendente de aprovação.');
            return;
        }

        $feeFixed   = (float) PlatformSetting::get('withdraw_fee_fixed', 0);
        $feePercent = (float) PlatformSetting::get('withdraw_fee_percent', 0);
        $fee        = round($feeFixed + ($this->valorSaqueAssin * $feePercent / 100), 2);
        $liquido    = round($this->valorSaqueAssin - $fee, 2);

        $wallet = WalletModel::firstOrCreate(
            ['user_id' => $user->id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => $minAmount, 'taxa_saque' => 0]
        );

        DB::transaction(function () use ($wallet, $user, $fee, $liquido) {
            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$this->valorSaqueAssin,
                'tipo'      => 'saque_solicitado',
                'fonte'     => 'assinaturas',
                'descricao' => 'Saque de Assinaturas: Kz ' . number_format($this->valorSaqueAssin, 0, ',', '.') . ' — taxa: Kz ' . number_format($fee, 2, ',', '.') . ' — a receber: Kz ' . number_format($liquido, 2, ',', '.') . ' — aguarda aprovação.',
            ]);
        });

        $this->saqueMsg    = 'Saque de Kz ' . number_format($this->valorSaqueAssin, 0, ',', '.') . ' solicitado! Receberá Kz ' . number_format($liquido, 0, ',', '.') . ' em até 2 dias úteis.';
        $this->saqueMsgType = 'success';
        $this->valorSaqueAssin = 0;
        $this->showSaqueModal  = false;
        $this->resetErrorBag('valorSaqueAssin');
    }
}
