<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;
use App\Models\WalletLog;
use App\Models\Wallet as WalletModel;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectManager extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    // ─── Saque dos Projectos ─────────────────────────────────────────
    public bool  $showSaqueModal   = false;
    public float $valorSaqueProjetos = 0;
    public string $saqueMsg         = '';
    public string $saqueMsgType     = 'success';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }

    public function abrirSaqueProjectos(): void
    {
        $this->saqueMsg      = '';
        $this->saqueMsgType  = 'success';
        $this->showSaqueModal = true;
    }

    public function fecharSaqueProjectos(): void
    {
        $this->showSaqueModal    = false;
        $this->valorSaqueProjetos = 0;
        $this->saqueMsg          = '';
    }

    public function solicitarSaqueProjectos(): void
    {
        $user = Auth::user();

        $totalGanhoProjetos = Service::where('freelancer_id', $user->id)
            ->where('status', 'completed')
            ->sum('valor_liquido');

        $totalSacadoProjetos = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'projetos')
            ->sum(DB::raw('ABS(valor)'));

        $saldoDisponivel = max(0, $totalGanhoProjetos - $totalSacadoProjetos);

        $minAmount = (float) PlatformSetting::get('withdrawal_min_amount', 1000);

        $this->validate([
            'valorSaqueProjetos' => ['required', 'numeric', 'min:' . $minAmount],
        ], [
            'valorSaqueProjetos.min' => 'O valor mínimo de saque é Kz ' . number_format($minAmount, 0, ',', '.') . '.',
        ]);

        if ($this->valorSaqueProjetos > $saldoDisponivel) {
            $this->addError('valorSaqueProjetos', 'Saldo insuficiente. Disponível: Kz ' . number_format($saldoDisponivel, 0, ',', '.') . '.');
            return;
        }

        $pendente = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'projetos')
            ->exists();

        if ($pendente) {
            $this->addError('valorSaqueProjetos', 'Já tem um saque de Projectos pendente de aprovação. Aguarde a resolução.');
            return;
        }

        $feeFixed   = (float) PlatformSetting::get('withdraw_fee_fixed', 0);
        $feePercent = (float) PlatformSetting::get('withdraw_fee_percent', 0);
        $fee        = round($feeFixed + ($this->valorSaqueProjetos * $feePercent / 100), 2);
        $liquido    = round($this->valorSaqueProjetos - $fee, 2);

        $wallet = WalletModel::firstOrCreate(
            ['user_id' => $user->id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => $minAmount, 'taxa_saque' => 0]
        );

        DB::transaction(function () use ($wallet, $user, $fee, $liquido) {
            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$this->valorSaqueProjetos,
                'tipo'      => 'saque_solicitado',
                'fonte'     => 'projetos',
                'descricao' => 'Saque de Projectos: Kz ' . number_format($this->valorSaqueProjetos, 0, ',', '.') . ' — taxa: Kz ' . number_format($fee, 2, ',', '.') . ' — a receber: Kz ' . number_format($liquido, 2, ',', '.') . ' — aguarda aprovação.',
            ]);
        });

        $this->saqueMsg      = 'Saque de Kz ' . number_format($this->valorSaqueProjetos, 0, ',', '.') . ' solicitado! Receberá Kz ' . number_format($liquido, 0, ',', '.') . ' em até 2 dias úteis.';
        $this->saqueMsgType  = 'success';
        $this->valorSaqueProjetos = 0;
        $this->showSaqueModal    = false;
        $this->resetErrorBag('valorSaqueProjetos');
    }

    public function render()
    {
        $user = Auth::user();
        $query = Service::where('freelancer_id', $user->id);
        if ($this->search) {
            $query->where('titulo', 'like', '%'.$this->search.'%');
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }
        $projects = $query->orderByDesc('created_at')->paginate(10);

        $statusCounts = Service::where('freelancer_id', $user->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $reviewedIds = \App\Models\Review::where('author_id', $user->id)
            ->whereIn('service_id', $projects->pluck('id'))
            ->pluck('service_id')
            ->toArray();

        // ── Saque ────────────────────────────────────────────────────────────
        $totalGanhoProjetos = Service::where('freelancer_id', $user->id)
            ->where('status', 'completed')
            ->sum('valor_liquido');
        $totalSacadoProjetos = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'projetos')
            ->sum(DB::raw('ABS(valor)'));
        $saldoProjetosDisponivel = max(0, $totalGanhoProjetos - $totalSacadoProjetos);
        $sakePendenteProjectos   = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'projetos')
            ->exists();

        return view('livewire.freelancer.project-manager', [
            'projects'               => $projects,
            'statusCounts'           => $statusCounts,
            'reviewedIds'            => $reviewedIds,
            'saldoProjetosDisponivel' => $saldoProjetosDisponivel,
            'sakePendenteProjectos'  => $sakePendenteProjectos,
        ])->layout('layouts.dashboard', [
            'dashboardTitle' => 'Meus Projetos',
        ]);
    }
}

