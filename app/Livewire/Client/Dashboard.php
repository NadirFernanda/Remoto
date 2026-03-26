<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProposalRejectedMail;
use App\Models\Service;
use App\Events\PaymentReceived;
use App\Events\ServiceCompleted;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\ProposalAcceptedNotification;
use App\Models\ServiceCandidate;
use App\Models\User;
use App\Models\Dispute;
use App\Models\Notification;
use App\Models\Wallet;
use App\Models\WalletLog;

class Dashboard extends Component
{
    public function liberarPagamento($serviceId)
    {
        $user = Auth::user();

        // Verificações rápidas antes do lock
        $serviceCheck = Service::where('id', $serviceId)->where('cliente_id', $user->id)->first();
        if (!$serviceCheck) {
            session()->flash('error', 'Pedido não encontrado.');
            return;
        }
        if ($serviceCheck->status !== 'delivered') {
            session()->flash('error', 'Só é possível liberar pagamento para pedidos entregues.');
            return;
        }
        if ($serviceCheck->is_payment_released) {
            session()->flash('info', 'O pagamento já foi liberado para este pedido.');
            return;
        }

        $freelancerPago = null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($serviceId, $user, &$freelancerPago) {
            $service = Service::where('id', $serviceId)
                ->where('cliente_id', $user->id)
                ->where('is_payment_released', false) // re-verifica no lock
                ->lockForUpdate()
                ->firstOrFail();

            $service->is_payment_released = true;
            $service->payment_released_at = now();
            $service->status = 'completed';
            $service->save();

            // Creditar valor líquido na carteira do freelancer
            if ($service->valor_liquido && $service->valor_liquido > 0 && $service->freelancer_id) {
                $freelancerWallet = Wallet::firstOrCreate(
                    ['user_id' => $service->freelancer_id],
                    ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
                );
                $freelancerWallet->increment('saldo', $service->valor_liquido);
                WalletLog::create([
                    'user_id'   => $service->freelancer_id,
                    'wallet_id' => $freelancerWallet->id,
                    'valor'     => $service->valor_liquido,
                    'tipo'      => 'pagamento_projeto',
                    'descricao' => 'Pagamento recebido pelo projeto: ' . $service->titulo,
                ]);

                // Libertar o escrow na carteira do cliente
                $clientWallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                if ($clientWallet && $clientWallet->saldo_pendente >= $service->valor) {
                    $clientWallet->decrement('saldo_pendente', $service->valor);
                }
            }

            Notification::create([
                'user_id'    => $service->freelancer_id,
                'service_id' => $service->id,
                'type'       => 'delivery_approved',
                'title'      => 'Entrega aprovada',
                'message'    => 'O cliente aprovou a sua entrega no projeto "' . $service->titulo . '". O pagamento foi creditado na sua carteira.',
            ]);

            $freelancerPago = User::find($service->freelancer_id);

            if ($freelancerPago) {
                PaymentReceived::dispatch($service, $freelancerPago, (float) ($service->valor_liquido ?? $service->valor));
            }
            ServiceCompleted::dispatch($service, $user, $freelancerPago ?? new User());
        });

        // Notificações de email fora da transacção (side-effects)
        if ($freelancerPago) {
            $service = Service::find($serviceId);
            $freelancerPago->notify(new PaymentReceivedNotification(
                $service,
                (float) ($service->valor_liquido ?? $service->valor),
                route('freelancer.wallet')
            ));
        }

        session()->flash('success', 'Pagamento liberado com sucesso!');
        $this->mount();
    }

    public function colocarEmModeracao($serviceId)
    {
        $user = Auth::user();
        $service = Service::where('id', $serviceId)->where('cliente_id', $user->id)->first();
        if (!$service) {
            session()->flash('error', 'Pedido não encontrado.');
            return;
        }
        if ($service->status === 'em_moderacao') {
            session()->flash('info', 'O pedido já está em moderação.');
            return;
        }

        $service->status = 'em_moderacao';
        $service->save();

        // Criar disputa automática se ainda não existir
        $dispute = Dispute::firstOrCreate(
            ['service_id' => $service->id],
            [
                'opened_by'   => $user->id,
                'reason'      => 'outro',
                'description' => 'Projeto colocado em moderação pelo cliente.',
                'status'      => 'aberta',
            ]
        );
        if ($dispute->wasRecentlyCreated) {
            $dispute->messages()->create([
                'user_id' => $user->id,
                'message' => 'O cliente colocou este projeto em moderação.',
            ]);
        }

        // Notificar todos os admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'    => $admin->id,
                'service_id' => $service->id,
                'type'       => 'moderation_requested',
                'title'      => 'Projeto em moderação',
                'message'    => 'O cliente colocou o projeto "' . $service->titulo . '" em moderação.',
            ]);
        }

        // Notificar o freelancer (se houver)
        if ($service->freelancer_id) {
            Notification::create([
                'user_id'    => $service->freelancer_id,
                'service_id' => $service->id,
                'type'       => 'moderation_requested',
                'title'      => 'Projeto em moderação',
                'message'    => 'O cliente colocou o projeto "' . $service->titulo . '" em moderação. Aguarde a intervenção da equipa.',
            ]);
        }

        session()->flash('success', 'Pedido colocado em moderação. A equipa de suporte foi notificada.');
        $this->mount();
    }
    public $orders = [];
    public $recent_messages = [];
    public $candidates = [];
    public $kpi_total_gasto = 0;
    public $kpi_projetos_publicados = 0;
    public $kpi_freelancers_contratados = 0;
    public $kpi_projetos_andamento = 0;
    public $kpi_projetos_concluidos = 0;
    public $period = 7;

    public function mount()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        // Redireciona para o dashboard correto se o role ativo não for cliente
        if (method_exists($user, 'activeRole') && $user->activeRole() !== 'cliente') {
            $activeRole = $user->activeRole();
            if ($activeRole === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('freelancer.dashboard');
        }
        $services = Service::where('cliente_id', $user->id)->get();
        $this->orders = $services->sortByDesc('created_at')->take(5);
        // Mensagens recentes
        $this->recent_messages = \App\Models\Message::with(['user', 'service'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Carrega candidatos para os serviços do cliente
        $serviceIds = $services->pluck('id')->all();
        $this->candidates = ServiceCandidate::with(['freelancer', 'service'])
            ->whereIn('service_id', $serviceIds)
            ->orderByDesc('created_at')
            ->get();
        // KPIs
        $this->kpi_total_gasto = $services->where('status', 'completed')->sum('valor');
        $this->kpi_projetos_publicados = $services->count();
        $this->kpi_freelancers_contratados = $services->whereNotNull('freelancer_id')->count();
        $this->kpi_projetos_andamento = $services->where('status', 'in_progress')->count();
        $this->kpi_projetos_concluidos = $services->where('status', 'completed')->count();
    }

    public function escolherFreelancer($serviceId, $freelancerId)
    {
        $user = Auth::user();

        // Verificações rápidas antes do lock
        $service = Service::where('id', $serviceId)->where('cliente_id', $user->id)->first();
        if (!$service) {
            session()->flash('error', 'Pedido não encontrado.');
            return;
        }
        if ($service->status !== 'published') {
            session()->flash('error', 'Só é possível escolher freelancer para pedidos publicados.');
            return;
        }
        $candidate = $service->candidates()->where('freelancer_id', $freelancerId)->first();
        if (!$candidate || !in_array($candidate->status, ['pending', 'proposal_sent', 'invited'])) {
            session()->flash('error', 'Candidato inválido ou já processado.');
            return;
        }

        // Operações atómicas com lock anti race-condition
        \Illuminate\Support\Facades\DB::transaction(function () use ($serviceId, $freelancerId, $candidate, $user) {
            $service = Service::where('id', $serviceId)
                ->where('cliente_id', $user->id)
                ->where('status', 'published')   // re-verifica dentro do lock
                ->lockForUpdate()
                ->firstOrFail();

            // Atualiza status do candidato escolhido
            $candidate->status = 'chosen';
            $candidate->save();
            // Rejeita os outros candidatos
            $service->candidates()->where('id', '!=', $candidate->id)->update(['status' => 'rejected']);
            // Atualiza o pedido
            $service->freelancer_id = $freelancerId;
            $service->status = 'in_progress';
            $service->save();

            // Notificar freelancer escolhido
            $mensagemEscolhido = 'Parabéns! Você foi escolhido para o projeto "' . $service->titulo . '". Acesse o painel para começar.';
            \App\Models\Notification::create([
                'user_id'    => $freelancerId,
                'service_id' => $service->id,
                'type'       => 'service_chosen',
                'title'      => 'Selecionado para projecto',
                'message'    => $mensagemEscolhido,
            ]);

            // Notificar freelancers rejeitados
            $rejeitados = $service->candidates()->where('status', 'rejected')->get();
            foreach ($rejeitados as $rej) {
                $mensagemRejeitado = 'Infelizmente você não foi selecionado para o projeto "' . $service->titulo . '". Não desanime, há outros projetos disponíveis!';
                \App\Models\Notification::create([
                    'user_id'    => $rej->freelancer_id,
                    'service_id' => $service->id,
                    'type'       => 'service_rejected',
                    'title'      => 'Não selecionado',
                    'message'    => $mensagemRejeitado,
                ]);
            }
        });

        // Email fora da transacção (side-effect)
        $freelancerEscolhido = User::find($freelancerId);
        if ($freelancerEscolhido) {
            $freelancerEscolhido->notify(new ProposalAcceptedNotification(
                Service::find($serviceId),
                route('freelancer.service.delivery', $serviceId)
            ));
        }

        // Emails de rejeição
        $rejeitadosEmail = Service::find($serviceId)->candidates()->where('status', 'rejected')->get();
        foreach ($rejeitadosEmail as $rej) {
            $freelancerRejeitado = User::find($rej->freelancer_id);
            if ($freelancerRejeitado) {
                Mail::to($freelancerRejeitado->email)
                    ->send(new ProposalRejectedMail($freelancerRejeitado, Service::find($serviceId), 'Infelizmente você não foi selecionado para o projeto'));
            }
        }

        session()->flash('success', 'Freelancer escolhido com sucesso! O projeto foi atualizado e todos os candidatos foram notificados.');
        $this->mount();
    }

    public function render()
    {
        $user = Auth::user();
        $affiliate_link = url('/register?ref=' . $user->affiliate_code);
        return view('livewire.client.dashboard', [
            'orders' => $this->orders,
            'recent_messages' => $this->recent_messages,
            'candidates' => $this->candidates,
            'kpi_total_gasto' => $this->kpi_total_gasto,
            'kpi_projetos_publicados' => $this->kpi_projetos_publicados,
            'kpi_freelancers_contratados' => $this->kpi_freelancers_contratados,
            'kpi_projetos_andamento' => $this->kpi_projetos_andamento,
            'kpi_projetos_concluidos' => $this->kpi_projetos_concluidos,
            'affiliate_link' => $affiliate_link,
            'period' => $this->period,
        ])->layout('layouts.dashboard', [
            'dashboardTitle' => 'Dashboard do Cliente',
        ]);
    }
}