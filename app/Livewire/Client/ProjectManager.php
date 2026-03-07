<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Service;
use App\Models\Milestone;
use App\Models\ServiceAttachment;
use Illuminate\Support\Facades\Storage;

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

        \App\Models\Notification::create([
            'user_id'    => $service->freelancer_id,
            'service_id' => $service->id,
            'type'       => 'delivery_approved',
            'message'    => 'O cliente aprovou sua entrega no projeto "' . $service->titulo . '".',
        ]);

        session()->flash('success', 'Entrega aprovada. Pagamento liberado para o freelancer.');
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

        return view('livewire.client.project-manager', compact(
            'projects', 'selected', 'statusLabels', 'pipeline'
        ))->layout('layouts.main');
    }
}
