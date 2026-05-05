<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Notification;
use App\Models\Refund;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Modules\Admin\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

class RefundsAdminPanel extends Component
{
    public string $status = '';
    public string $search = '';

    // Aprovação parcial: guarda o valor editado por refund ID
    public array $valoresReembolso = [];

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function approve(int $id): void
    {
        DB::transaction(function () use ($id) {
            $refund = Refund::with('service')->where('id', $id)->lockForUpdate()->firstOrFail();

            if ($refund->status === 'aprovado') {
                session()->flash('info', 'Este reembolso já foi processado.');
                return;
            }

            $service     = $refund->service;
            $valorTotal  = $service ? (float)($service->valor ?? 0) : 0;

            // Admin pode definir valor parcial via input; padrão = valor total
            $valorInput  = isset($this->valoresReembolso[$id])
                ? (float) $this->valoresReembolso[$id]
                : $valorTotal;

            $valorFinal = max(0, min($valorInput, $valorTotal));

            $refund->valor_reembolso = $valorFinal;
            $refund->status          = 'aprovado';
            $refund->save();

            if ($valorFinal > 0) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $refund->user_id],
                    ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
                );
                $wallet->increment('saldo', $valorFinal);

                WalletLog::create([
                    'user_id'   => $refund->user_id,
                    'wallet_id' => $wallet->id,
                    'valor'     => $valorFinal,
                    'tipo'      => 'reembolso_aprovado',
                    'fonte'     => 'projetos',
                    'descricao' => 'Reembolso aprovado pelo admin'
                        . ($service ? " — projeto: {$service->titulo}" : '')
                        . ($valorFinal < $valorTotal ? " (parcial: {$valorFinal} de {$valorTotal} Kz)" : '')
                        . '.',
                ]);

                // Se parcial, devolver o restante ao freelancer (se existir)
                if ($valorFinal < $valorTotal && $service?->freelancer_id) {
                    $restante = $valorTotal - $valorFinal;
                    $freelancerWallet = Wallet::firstOrCreate(['user_id' => $service->freelancer_id]);
                    $freelancerWallet->increment('saldo', $restante);

                    WalletLog::create([
                        'user_id'   => $service->freelancer_id,
                        'wallet_id' => $freelancerWallet->id,
                        'valor'     => $restante,
                        'tipo'      => 'reembolso_parcial_freelancer',
                        'fonte'     => 'projetos',
                        'descricao' => "Compensação de reembolso parcial — cliente recebeu {$valorFinal} Kz, freelancer retém {$restante} Kz do projeto {$service->titulo}.",
                    ]);

                    Notification::create([
                        'user_id' => $service->freelancer_id,
                        'type'    => 'reembolso_parcial',
                        'title'   => 'Reembolso parcial processado',
                        'message' => "O cliente recebeu reembolso de {$valorFinal} Kz no projeto \"{$service->titulo}\". O teu saldo foi creditado com {$restante} Kz.",
                    ]);
                }
            }

            AuditLogger::log(
                'reembolso_aprovado',
                "Reembolso #{$id} aprovado — valor: {$valorFinal} Kz",
                'Refund',
                $id
            );
        });

        $refund = Refund::find($id);
        $valor  = $refund?->valor_reembolso ?? 0;

        Notification::create([
            'user_id'     => $refund->user_id,
            'type'        => 'refund_approved',
            'target_role' => $refund->user?->role,
            'title'       => 'Reembolso aprovado',
            'message'     => $valor > 0
                ? "O teu reembolso de " . number_format($valor, 0, ',', '.') . " Kz foi aprovado e creditado na tua carteira."
                : 'O teu pedido de reembolso foi aprovado.',
        ]);

        session()->flash('success', 'Reembolso aprovado e cliente notificado.');
    }

    public function reject(int $id): void
    {
        DB::transaction(function () use ($id) {
            $refund = Refund::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($refund->status === 'rejeitado') {
                return;
            }

            $refund->update(['status' => 'rejeitado']);

            Notification::create([
                'user_id'     => $refund->user_id,
                'type'        => 'refund_rejected',
                'target_role' => $refund->user?->role,
                'title'       => 'Reembolso rejeitado',
                'message'     => 'O teu pedido de reembolso foi rejeitado pelo admin.',
            ]);

            AuditLogger::log('reembolso_rejeitado', "Reembolso #{$id} rejeitado", 'Refund', $id);
        });

        session()->flash('success', 'Reembolso rejeitado e cliente notificado.');
    }

    public function render()
    {
        $refunds = Refund::with(['user', 'service'])
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->search, fn($q) => $q->where('reason', 'like', '%' . $this->search . '%'))
            ->orderByDesc('created_at')
            ->paginate(15);

        // Pré-preencher valoresReembolso com os valores totais dos serviços
        foreach ($refunds as $refund) {
            if (!isset($this->valoresReembolso[$refund->id])) {
                $this->valoresReembolso[$refund->id] = $refund->valor_reembolso
                    ?? $refund->service?->valor
                    ?? 0;
            }
        }

        return view('livewire.admin.refunds-admin-panel', compact('refunds'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Painel de Reembolsos']);
    }
}
