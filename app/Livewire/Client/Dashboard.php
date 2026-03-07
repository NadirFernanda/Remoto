<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Service;
use App\Models\ServiceCandidate;
use App\Models\User;
use App\Models\Dispute;
use App\Models\Notification;

class Dashboard extends Component
{
    public function liberarPagamento($serviceId)
    {
        $user = Auth::user();
        $service = Service::where('id', $serviceId)->where('cliente_id', $user->id)->first();
        if (!$service) {
            session()->flash('error', 'Pedido não encontrado.');
            return;
        }
        if ($service->status !== 'delivered') {
            session()->flash('error', 'Só é possível liberar pagamento para pedidos entregues.');
            return;
        }
        if ($service->is_payment_released) {
            session()->flash('info', 'O pagamento já foi liberado para este pedido.');
            return;
        }
        $service->is_payment_released = true;
        $service->payment_released_at = now();
        $service->status = 'completed';
        $service->save();
        // TODO: Notificar admin e freelancer
        session()->flash('success', 'Pagamento liberado com sucesso! O admin será notificado para processar o pagamento ao freelancer.');
        $this->mount(); // Atualiza a lista
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

    public function mount()
    {
        \Log::debug('DASHBOARD CLIENTE: mount iniciado');
        $user = Auth::user();
        \Log::debug('DASHBOARD CLIENTE: user', ['user' => $user]);
        if (!$user) {
            \Log::error('DASHBOARD CLIENTE: Usuário não autenticado');
            throw new \Exception('Usuário não autenticado. Faça login para acessar o dashboard do cliente.');
        }
        $services = Service::where('cliente_id', $user->id)->get();
        \Log::debug('DASHBOARD CLIENTE: services', ['services' => $services]);
        if ($services === null) {
            \Log::error('DASHBOARD CLIENTE: Nenhum serviço retornado para o cliente');
            throw new \Exception('Nenhum serviço encontrado para o cliente.');
        }
        $this->orders = $services->sortByDesc('created_at')->take(5);
        \Log::debug('DASHBOARD CLIENTE: orders', ['orders' => $this->orders]);
        // Mensagens recentes (exemplo básico)
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
        \Log::debug('DASHBOARD CLIENTE: recent_messages', ['recent_messages' => $this->recent_messages]);
        // KPIs
        $this->kpi_total_gasto = $services->where('status', 'completed')->sum('valor');
        $this->kpi_projetos_publicados = $services->count();
        $this->kpi_freelancers_contratados = $services->whereNotNull('freelancer_id')->count();
        $this->kpi_projetos_andamento = $services->where('status', 'in_progress')->count();
        $this->kpi_projetos_concluidos = $services->where('status', 'completed')->count();
        \Log::debug('DASHBOARD CLIENTE: mount finalizado');
    }

    public function escolherFreelancer($serviceId, $freelancerId)
    {
        $user = Auth::user();
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
        if (!$candidate || $candidate->status !== 'pending') {
            session()->flash('error', 'Candidato inválido ou já processado.');
            return;
        }
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
            'message'    => $mensagemEscolhido,
        ]);
        $freelancerEscolhido = User::find($freelancerId);
        if ($freelancerEscolhido) {
            Mail::raw($mensagemEscolhido, function ($mail) use ($freelancerEscolhido, $service) {
                $mail->to($freelancerEscolhido->email)
                     ->subject('Você foi escolhido para o projeto "' . $service->titulo . '"');
            });
        }

        // Notificar freelancers rejeitados
        $rejeitados = $service->candidates()->where('status', 'rejected')->get();
        foreach ($rejeitados as $rej) {
            $mensagemRejeitado = 'Infelizmente você não foi selecionado para o projeto "' . $service->titulo . '". Não desanime, há outros projetos disponíveis!';
            \App\Models\Notification::create([
                'user_id'    => $rej->freelancer_id,
                'service_id' => $service->id,
                'type'       => 'service_rejected',
                'message'    => $mensagemRejeitado,
            ]);
            $freelancerRejeitado = User::find($rej->freelancer_id);
            if ($freelancerRejeitado) {
                Mail::raw($mensagemRejeitado, function ($mail) use ($freelancerRejeitado, $service) {
                    $mail->to($freelancerRejeitado->email)
                         ->subject('Atualização sobre o projeto "' . $service->titulo . '"');
                });
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
        ])->layout('layouts.dashboard', [
            'dashboardTitle' => 'Dashboard do Cliente',
        ]);
    }
}