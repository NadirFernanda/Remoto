<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AuditLog;

class AuditLogs extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $actionFilter = '';
    public string $entityFilter = '';
    public string $dateFrom    = '';
    public string $dateTo      = '';
    public ?int $expandedId    = null;

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function updatingSearch(): void      { $this->resetPage(); }
    public function updatingActionFilter(): void { $this->resetPage(); }
    public function updatingEntityFilter(): void { $this->resetPage(); }
    public function updatingDateFrom(): void     { $this->resetPage(); }
    public function updatingDateTo(): void       { $this->resetPage(); }

    public function toggleExpand(int $id): void
    {
        $this->expandedId = $this->expandedId === $id ? null : $id;
    }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, fn($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->actionFilter, fn($q) => $q->where('action', $this->actionFilter))
            ->when($this->entityFilter, fn($q) => $q->where('entity_type', $this->entityFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderByDesc('created_at')
            ->paginate(50);

        $actions  = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $entities = AuditLog::whereNotNull('entity_type')->select('entity_type')->distinct()->orderBy('entity_type')->pluck('entity_type');

        return view('livewire.admin.audit-logs', compact('logs', 'actions', 'entities'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Logs e Auditoria']);
    }
}
