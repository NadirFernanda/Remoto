<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WalletLog;
use App\Models\Wallet;
use App\Models\Notification;
use App\Services\AuditLogger;
use Carbon\Carbon;

class Payouts extends Component
{
    use WithPagination;

    public string $period = 'month';
    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPeriod(): void  { $this->resetPage(); }

    public function aprovarSaque(int $logId): void
    {
        $log = WalletLog::where('id', $logId)->where('tipo', 'saque_solicitado')->firstOrFail();

        $log->update(['tipo' => 'saque_aprovado']);

        Notification::create([
            'user_id'   => $log->user_id,
            'type'      => 'saque_aprovado',
            'title'     => 'Saque aprovado',
            'message'   => 'O seu saque de ' . number_format(abs($log->valor), 0, ',', '.') . ' Kz foi aprovado e será processado em breve.',
        ]);

        AuditLogger::log('saque_aprovado', "Saque #{$log->id} de {$log->valor} Kz aprovado pelo admin", 'WalletLog', $log->id);

        session()->flash('success', 'Saque aprovado e freelancer notificado.');
    }

    public function rejeitarSaque(int $logId): void
    {
        $log = WalletLog::where('id', $logId)->where('tipo', 'saque_solicitado')->firstOrFail();

        // Devolver o saldo ao freelancer
        $wallet = Wallet::where('id', $log->wallet_id)->first();
        if ($wallet) {
            $wallet->increment('saldo', abs($log->valor));
        }

        $log->update(['tipo' => 'saque_rejeitado']);

        // Log de crédito de devolução
        WalletLog::create([
            'user_id'   => $log->user_id,
            'wallet_id' => $log->wallet_id,
            'valor'     => abs($log->valor),
            'tipo'      => 'saque_devolvido',
            'descricao' => 'Saque rejeitado pelo admin — valor devolvido ao saldo.',
        ]);

        Notification::create([
            'user_id'   => $log->user_id,
            'type'      => 'saque_rejeitado',
            'title'     => 'Saque rejeitado',
            'message'   => 'O seu pedido de saque de ' . number_format(abs($log->valor), 0, ',', '.') . ' Kz foi rejeitado. O valor foi devolvido ao seu saldo.',
        ]);

        AuditLogger::log('saque_rejeitado', "Saque #{$log->id} rejeitado — valor devolvido", 'WalletLog', $log->id);

        session()->flash('success', 'Saque rejeitado e saldo devolvido ao freelancer.');
    }

    public function render()
    {
        $start = match ($this->period) {
            'week'  => Carbon::now()->startOfWeek(),
            'year'  => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        // Saques pendentes (aguardam aprovação)
        $pendentes = WalletLog::with('user')
            ->where('tipo', 'saque_solicitado')
            ->when($this->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%'.$this->search.'%')))
            ->orderByDesc('created_at')
            ->get();

        // Histórico de saques processados
        $logs = WalletLog::with('user')
            ->whereIn('tipo', ['saque_aprovado', 'saque_rejeitado'])
            ->where('created_at', '>=', $start)
            ->when($this->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%'.$this->search.'%')))
            ->orderByDesc('created_at')
            ->paginate(50);

        $totalAprovado = WalletLog::where('tipo', 'saque_aprovado')
            ->where('created_at', '>=', $start)
            ->sum('valor');

        return view('livewire.admin.payouts', compact('logs', 'pendentes', 'totalAprovado'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Saques']);
    }
}
