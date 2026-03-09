<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Refund;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\Notification;

class RefundsAdminPanel extends Component
{
    public $status = '';
    public $search = '';

    public function approve($id)
    {
        $refund = Refund::with('service')->find($id);
        if (!$refund) return;

        $refund->status = 'aprovado';
        $refund->save();

        // Creditar valor na carteira do cliente
        $service = $refund->service;
        $valor   = $service ? ($service->valor ?? 0) : 0;
        if ($valor > 0) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $refund->user_id],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
            );
            $wallet->increment('saldo', $valor);
            WalletLog::create([
                'user_id'   => $refund->user_id,
                'wallet_id' => $wallet->id,
                'valor'     => $valor,
                'tipo'      => 'reembolso_aprovado',
                'descricao' => 'Reembolso aprovado pelo admin' . ($service ? ' — projeto: ' . $service->titulo : '') . '.',
            ]);
        }

        Notification::create([
            'user_id' => $refund->user_id,
            'type'    => 'refund_approved',
            'title'   => 'Reembolso aprovado',
            'message' => 'O seu pedido de reembolso foi aprovado' . ($valor > 0 ? ' e ' . number_format($valor, 0, ',', '.') . ' Kz foram creditados na sua carteira.' : '.'),
        ]);

        session()->flash('success', 'Reembolso aprovado e cliente notificado.');
    }

    public function reject($id)
    {
        $refund = Refund::find($id);
        if (!$refund) return;

        $refund->status = 'rejeitado';
        $refund->save();

        Notification::create([
            'user_id' => $refund->user_id,
            'type'    => 'refund_rejected',
            'title'   => 'Reembolso rejeitado',
            'message' => 'O seu pedido de reembolso foi rejeitado pelo admin.',
        ]);

        session()->flash('success', 'Reembolso rejeitado e cliente notificado.');
    }

    public function render()
    {
        $refunds = Refund::query()
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->search, fn($q) => $q->where('reason', 'like', '%'.$this->search.'%'))
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('livewire.admin.refunds-admin-panel', compact('refunds'));
    }
}
