<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\Notification;
use App\Notifications\ServiceCancelledNotification;
use Illuminate\Support\Facades\Auth;

class ServiceCancel extends Component
{
    public Service $service;

    public function mount(Service $service)
    {
        $this->service = $service;
    }

    public function cancelService()
    {
        if (!Auth::user()->can('cancel', $this->service)) {
            session()->flash('error', 'Ação não permitida.');
            return;
        }

        $status = $this->service->status;

        // Projectos em andamento só podem ser cancelados via disputa
        if ($status === 'in_progress') {
            session()->flash('error', 'Projectos em andamento só podem ser cancelados através de uma disputa.');
            return;
        }

        if (!in_array($status, ['published', 'accepted'])) {
            session()->flash('error', 'Não é possível cancelar este pedido no estado actual.');
            return;
        }

        // Se tem escrow retido (escolheu freelancer mas ainda não entregue)
        if ($status === 'accepted' && $this->service->freelancer_id) {
            $this->_devolverEscrow();

            // Notificar o freelancer
            Notification::create([
                'user_id'    => $this->service->freelancer_id,
                'service_id' => $this->service->id,
                'type'       => 'project_cancelled',
                'title'      => 'Projecto cancelado',
                'message'    => 'O cliente cancelou o projecto "' . $this->service->titulo . '". O pagamento em escrow foi estornado.',
            ]);
            $freelancerCancelado = \App\Models\User::find($this->service->freelancer_id);
            if ($freelancerCancelado) {
                $freelancerCancelado->notify(new ServiceCancelledNotification(
                    $this->service,
                    Auth::user(),
                    route('freelancer.dashboard')
                ));
            }
        }

        // Log de reembolso para projectos published (pagamento via gateway externo)
        if ($status === 'published' && $this->service->valor) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => Auth::id()],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
            );
            WalletLog::create([
                'user_id'   => Auth::id(),
                'wallet_id' => $wallet->id,
                'valor'     => $this->service->valor,
                'tipo'      => 'reembolso_solicitado',
                'descricao' => 'Reembolso pendente pelo cancelamento do projecto: ' . $this->service->titulo,
            ]);
        }

        $this->service->status = 'cancelled';
        $this->service->save();

        session()->flash('success', 'Pedido cancelado. ' . ($this->service->valor ? 'O reembolso será processado em até 5 dias úteis.' : ''));
        return redirect()->route('client.orders');
    }

    private function _devolverEscrow(): void
    {
        $clientWallet = Wallet::where('user_id', Auth::id())->first();
        if ($clientWallet && $this->service->valor && $clientWallet->saldo_pendente >= $this->service->valor) {
            $clientWallet->decrement('saldo_pendente', $this->service->valor);
            $clientWallet->increment('saldo', $this->service->valor);
            WalletLog::create([
                'user_id'   => Auth::id(),
                'wallet_id' => $clientWallet->id,
                'valor'     => $this->service->valor,
                'tipo'      => 'reembolso_escrow',
                'descricao' => 'Escrow devolvido por cancelamento do projecto: ' . $this->service->titulo,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.client.service-cancel', [
            'service' => $this->service
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Cancelar Serviço']);
    }
}
