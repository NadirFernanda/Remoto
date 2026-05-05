<?php

namespace App\Livewire\Admin;

use App\Models\Notification;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Modules\Admin\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class WalletAdjustment extends Component
{
    use WithPagination;

    public string $searchUser  = '';
    public ?int   $userId      = null;
    public string $userName    = '';
    public float  $amount      = 0;
    public string $reason      = '';
    public string $periodFilter = 'month';

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function selectUser(int $id, string $name): void
    {
        $this->userId   = $id;
        $this->userName = $name;
        $this->searchUser = '';
    }

    public function clearUser(): void
    {
        $this->userId   = null;
        $this->userName = '';
        $this->amount   = 0;
        $this->reason   = '';
    }

    public function applyAdjustment(): void
    {
        $this->validate([
            'userId' => 'required|exists:users,id',
            'amount' => 'required|numeric|not_in:0',
            'reason' => 'required|string|min:10|max:500',
        ], [
            'amount.not_in'  => 'O valor não pode ser zero.',
            'reason.min'     => 'O motivo deve ter pelo menos 10 caracteres.',
        ]);

        $user  = User::findOrFail($this->userId);
        $admin = auth()->user();
        $abs   = abs($this->amount);
        $isCredit = $this->amount > 0;

        DB::transaction(function () use ($user, $admin, $abs, $isCredit) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
            );
            $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            if (!$isCredit && $wallet->saldo < $abs) {
                $this->addError('amount', 'Saldo insuficiente para debitar este valor.');
                return;
            }

            if ($isCredit) {
                $wallet->increment('saldo', $abs);
            } else {
                $wallet->decrement('saldo', $abs);
            }

            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => $isCredit ? $abs : -$abs,
                'tipo'      => 'ajuste_admin',
                'fonte'     => 'geral',
                'descricao' => "Ajuste manual por {$admin->name}: {$this->reason}",
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type'    => 'ajuste_admin',
                'title'   => $isCredit ? 'Crédito adicionado' : 'Débito aplicado',
                'message' => ($isCredit
                    ? 'O admin creditou ' . number_format($abs, 0, ',', '.') . ' Kz na tua carteira.'
                    : 'O admin debitou ' . number_format($abs, 0, ',', '.') . ' Kz da tua carteira.')
                    . ' Motivo: ' . $this->reason,
            ]);

            AuditLogger::log(
                'ajuste_admin_wallet',
                ($isCredit ? 'Crédito' : 'Débito') . " de {$abs} Kz na carteira de {$user->name} (ID: {$user->id}). Motivo: {$this->reason}",
                'Wallet',
                $wallet->id
            );
        });

        if ($this->getErrorBag()->isEmpty()) {
            $this->reset(['amount', 'reason', 'userId', 'userName']);
            session()->flash('success', 'Ajuste aplicado e utilizador notificado.');
        }
    }

    public function updatingSearchUser(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = collect();
        if (strlen($this->searchUser) >= 2) {
            $users = User::where('name', 'like', '%' . $this->searchUser . '%')
                ->orWhere('email', 'like', '%' . $this->searchUser . '%')
                ->limit(10)
                ->get(['id', 'name', 'email', 'role']);
        }

        $start = match ($this->periodFilter) {
            'week'  => now()->startOfWeek(),
            'year'  => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $logs = WalletLog::with('user')
            ->where('tipo', 'ajuste_admin')
            ->where('created_at', '>=', $start)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('livewire.admin.wallet-adjustment', compact('users', 'logs'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Ajuste de Saldo']);
    }
}
