<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Service;
use App\Models\ServiceCandidate;
use App\Models\Milestone;
use App\Models\ServiceAttachment;
use App\Models\Notification;
use App\Models\Review;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Notifications\ProposalAcceptedNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ProjectManager extends Component
{
    use WithFileUploads;

    public string $statusFilter = '';

    // Selected project
    public ?int $selectedServiceId = null;

    // Milestone form
    public string $milestoneTitle = '';
    public string $milestoneDate  = '';
    public string $milestoneDesc  = '';

    // Attachment upload
    public $attachmentFile = null;

    public function selectService(int $id): void
    {
        $this->selectedServiceId = $id;
        $this->resetMilestoneForm();
        $this->attachmentFile = null;
    }

    // ─── Milestones ────────────────────────────────────────
    public function addMilestone(): void
    {
        $this->validate([
            'milestoneTitle' => 'required|string|max:200',
            'milestoneDate'  => 'nullable|date',
            'milestoneDesc'  => 'nullable|string|max:500',
        ]);

        $service = $this->loadSelectedService();

        Milestone::create([
            'service_id'  => $service->id,
            'title'       => $this->milestoneTitle,
            'description' => $this->milestoneDesc ?: null,
            'due_date'    => $this->milestoneDate ?: null,
            'sort_order'  => $service->milestones()->count(),
        ]);

        $this->resetMilestoneForm();
        session()->flash('success', 'Marco adicionado.');
    }

    public function toggleMilestone(int $milestoneId): void
    {
        $milestone = Milestone::whereHas('service', fn($q) => $q->where('cliente_id', auth()->id()))
            ->findOrFail($milestoneId);

        $milestone->completed
            ? $milestone->markIncomplete()
            : $milestone->markComplete();
    }

    public function deleteMilestone(int $milestoneId): void
    {
        Milestone::whereHas('service', fn($q) => $q->where('cliente_id', auth()->id()))
            ->findOrFail($milestoneId)
            ->delete();
    }

    // ─── Attachments ───────────────────────────────────────
    public function uploadAttachment(): void
    {
        $this->validate([
            'attachmentFile' => 'required|file|max:20480',
        ]);

        $service = $this->loadSelectedService();
        $file    = $this->attachmentFile;
        $path    = $file->store("attachments/{$service->id}", 'public');

        ServiceAttachment::create([
            'service_id' => $service->id,
            'user_id'    => auth()->id(),
            'filename'   => $file->getClientOriginalName(),
            'path'       => $path,
            'size'       => $file->getSize(),
            'mime_type'  => $file->getMimeType(),
        ]);

        $this->attachmentFile = null;
        session()->flash('success', 'Ficheiro enviado.');
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $att = ServiceAttachment::whereHas('service', fn($q) => $q->where('cliente_id', auth()->id()))
            ->findOrFail($attachmentId);

        Storage::disk('public')->delete($att->path);
        $att->delete();
        session()->flash('success', 'Ficheiro removido.');
    }

    // ─── Proposals / Candidates ─────────────────────────────
    public function escolherFreelancer(int $serviceId, int $freelancerId): void
    {
        $service = Service::where('id', $serviceId)->where('cliente_id', auth()->id())->firstOrFail();

        if ($service->status !== 'published') {
            session()->flash('error', 'Só é possível escolher freelancer para projetos publicados.');
            return;
        }

        $candidate = $service->candidates()->where('freelancer_id', $freelancerId)->first();
        if (!$candidate || !in_array($candidate->status, ['pending', 'proposal_sent', 'invited'])) {
            session()->flash('error', 'Candidato inválido ou já processado.');
            return;
        }

        // Escolhe o candidato
        $candidate->status = 'chosen';
        $candidate->save();

        // Rejeita os outros
        $service->candidates()->where('id', '!=', $candidate->id)->update(['status' => 'rejected']);

        // Se o freelancer propôs um valor, usar esse como valor final do serviço
        if ($candidate->proposal_value && $candidate->proposal_value > 0) {
            $service->valor         = (float) $candidate->proposal_value;
            $service->taxa          = 10.0;
            $service->valor_liquido = round($candidate->proposal_value * 0.80, 2); // 80% para o freelancer, 20% taxa plataforma
        }

        // Atualiza o projeto
        $service->freelancer_id = $freelancerId;
        $service->status = 'in_progress';
        $service->save();

        // Registar retenção em escrow na carteira do cliente
        if ($service->valor && $service->valor > 0) {
            $clientWallet = Wallet::firstOrCreate(
                ['user_id' => auth()->id()],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
            );
            $clientWallet->increment('saldo_pendente', $service->valor);
            WalletLog::create([
                'user_id'   => auth()->id(),
                'wallet_id' => $clientWallet->id,
                'valor'     => -$service->valor,
                'tipo'      => 'escrow_retido',
                'descricao' => 'Pagamento retido em escrow para o projeto: ' . $service->titulo,
            ]);
        }

        // Notifica o freelancer escolhido
        Notification::create([
            'user_id'    => $freelancerId,
            'service_id' => $service->id,
            'type'       => 'service_chosen',
            'title'      => 'Selecionado para projeto',
            'message'    => 'Parabéns! Você foi escolhido para o projeto "' . $service->titulo . '".',
        ]);
        $freelancerEscolhidoPM = User::find($freelancerId);
        if ($freelancerEscolhidoPM) {
            $freelancerEscolhidoPM->notify(new ProposalAcceptedNotification(
                $service,
                route('freelancer.projects')
            ));
        }

        // Notifica os rejeitados
        $rejeitados = $service->candidates()->where('status', 'rejected')->get();
        foreach ($rejeitados as $rej) {
            Notification::create([
                'user_id'    => $rej->freelancer_id,
                'service_id' => $service->id,
                'type'       => 'service_rejected',
                'title'      => 'Proposta não selecionada',
                'message'    => 'Infelizmente não foi selecionado para o projeto "' . $service->titulo . '".',
            ]);
        }

        session()->flash('success', 'Freelancer escolhido! O projeto foi atualizado e todos os candidatos foram notificados.');
        $this->selectedServiceId = $service->id;
    }

    public function rejeitarFreelancer(int $serviceId, int $freelancerId): void
    {
        $service = Service::where('id', $serviceId)->where('cliente_id', auth()->id())->firstOrFail();

        $candidate = $service->candidates()->where('freelancer_id', $freelancerId)->first();
        if (!$candidate) {
            session()->flash('error', 'Candidato não encontrado.');
            return;
        }

        $candidate->status = 'rejected';
        $candidate->save();

        Notification::create([
            'user_id'    => $freelancerId,
            'service_id' => $service->id,
            'type'       => 'service_rejected',
            'title'      => 'Proposta rejeitada',
            'message'    => 'A sua proposta para o projeto "' . $service->titulo . '" foi rejeitada.',
        ]);

        session()->flash('success', 'Candidato rejeitado e notificado.');
    }

    // ─── Delivery approval ─────────────────────────────────
    public function approveDelivery(int $serviceId): void
    {
        $service = Service::where('id', $serviceId)->where('cliente_id', auth()->id())->firstOrFail();

        if ($service->status !== 'delivered') {
            session()->flash('error', 'Apenas serviços entregues podem ser aprovados.');
            return;
        }

        $service->update([
            'status'               => 'completed',
            'is_payment_released'  => true,
            'payment_released_at'  => now(),
        ]);

        // Creditar valor líquido na carteira do freelancer
        if ($service->valor_liquido && $service->valor_liquido > 0) {
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
            $clientWallet = Wallet::where('user_id', $service->cliente_id)->first();
            if ($clientWallet && $clientWallet->saldo_pendente >= $service->valor) {
                $clientWallet->decrement('saldo_pendente', $service->valor);
                WalletLog::create([
                    'user_id'   => $service->cliente_id,
                    'wallet_id' => $clientWallet->id,
                    'valor'     => 0,
                    'tipo'      => 'escrow_liberado',
                    'descricao' => 'Escrow liberado após aprovação do projeto: ' . $service->titulo,
                ]);
            }
        }

        Notification::create([
            'user_id'    => $service->freelancer_id,
            'service_id' => $service->id,
            'type'       => 'delivery_approved',
            'message'    => 'O cliente aprovou a sua entrega no projeto "' . $service->titulo . '". O pagamento foi creditado na sua carteira.',
        ]);

        session()->flash('success', 'Entrega aprovada. ' . number_format($service->valor_liquido, 2, ',', '.') . ' Kz creditados na carteira do freelancer.');
    }

    public function requestRevision(int $serviceId): void
    {
        $service = Service::where('id', $serviceId)->where('cliente_id', auth()->id())->firstOrFail();

        if ($service->status !== 'delivered') {
            session()->flash('error', 'Ação inválida para o estado atual.');
            return;
        }

        $service->update(['status' => 'in_progress']);

        \App\Models\Notification::create([
            'user_id'    => $service->freelancer_id,
            'service_id' => $service->id,
            'type'       => 'revision_requested',
            'message'    => 'O cliente solicitou revisão no projeto "' . $service->titulo . '".',
        ]);

        session()->flash('success', 'Revisão solicitada. O freelancer foi notificado.');
    }

    // ─── Helpers ───────────────────────────────────────────
    private function loadSelectedService(): Service
    {
        return Service::where('id', $this->selectedServiceId)
            ->where('cliente_id', auth()->id())
            ->firstOrFail();
    }

    private function resetMilestoneForm(): void
    {
        $this->milestoneTitle = '';
        $this->milestoneDate  = '';
        $this->milestoneDesc  = '';
        $this->resetValidation(['milestoneTitle', 'milestoneDate', 'milestoneDesc']);
    }

    public function render()
    {
        $statusLabels = [
            'published'    => 'Publicado',
            'accepted'     => 'Proposta Aceita',
            'in_progress'  => 'Em Andamento',
            'delivered'    => 'Aguardando Revisão',
            'completed'    => 'Concluído',
            'cancelled'    => 'Cancelado',
            'em_moderacao' => 'Em Moderação',
        ];

        $query = Service::where('cliente_id', auth()->id())
            ->with(['milestones', 'attachments', 'freelancer'])
            ->orderByDesc('updated_at');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $projects = $query->get();

        // Pipeline counts
        $pipeline = Service::where('cliente_id', auth()->id())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $selected = $this->selectedServiceId
            ? $projects->firstWhere('id', $this->selectedServiceId)
            : null;

        $candidates = $selected
            ? $selected->candidates()->with('freelancer.freelancerProfile')->whereIn('status', ['pending', 'proposal_sent', 'invited', 'rejected'])->orderByDesc('created_at')->get()
            : collect();

        // Check if client already left a review for the selected project
        $hasReview = $selected
            ? Review::where('author_id', auth()->id())->where('service_id', $selected->id)->exists()
            : false;

        return view('livewire.client.project-manager', compact(
            'projects', 'selected', 'statusLabels', 'pipeline', 'candidates', 'hasReview'
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'Os meus Projectos']);
    }
}
