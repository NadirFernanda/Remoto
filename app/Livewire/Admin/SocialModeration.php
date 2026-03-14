<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialReport;
use App\Models\SocialPost;
use App\Models\User;

class SocialModeration extends Component
{
    use WithPagination;

    public string $filterStatus = 'pendente';
    public string $filterType = '';
    public ?int $viewingReportId = null;
    public string $adminNote = '';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'adminNote' => 'nullable|string|max:1000',
    ];

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function render()
    {
        $query = SocialReport::with('reporter')
            ->when($this->filterStatus !== '', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType !== '', fn($q) => $q->where('reportable_type', $this->filterType))
            ->latest();

        $reports = $query->paginate(20);

        // Counts for tab badges
        $counts = [
            'pendente'  => SocialReport::where('status', 'pendente')->count(),
            'resolvido' => SocialReport::where('status', 'resolvido')->count(),
            'ignorado'  => SocialReport::where('status', 'ignorado')->count(),
        ];

        return view('livewire.admin.social-moderation', compact('reports', 'counts'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Moderação Social']);
    }

    public function viewReport(int $reportId): void
    {
        $this->viewingReportId = $reportId;
        $report = SocialReport::find($reportId);
        $this->adminNote = $report->admin_note ?? '';
    }

    public function closeReport(): void
    {
        $this->viewingReportId = null;
        $this->adminNote = '';
    }

    public function resolve(int $reportId): void
    {
        $this->updateReport($reportId, 'resolvido');
    }

    public function ignore(int $reportId): void
    {
        $this->updateReport($reportId, 'ignorado');
    }

    public function removeContent(int $reportId): void
    {
        $report = SocialReport::findOrFail($reportId);

        if ($report->reportable_type === 'post') {
            $post = SocialPost::find($report->reportable_id);
            if ($post) {
                foreach ($post->images as $image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($image->path);
                }
                $post->update(['status' => 'removed']);
            }
        }

        // Resolve all reports for this content
        SocialReport::where('reportable_type', $report->reportable_type)
            ->where('reportable_id', $report->reportable_id)
            ->update(['status' => 'resolvido', 'admin_note' => 'Conteúdo removido pelo administrador.']);

        $this->closeReport();
        session()->flash('success', 'Conteúdo removido e denúncias resolvidas.');
    }

    public function saveNote(int $reportId): void
    {
        $this->validateOnly('adminNote');
        SocialReport::where('id', $reportId)->update(['admin_note' => $this->adminNote]);
        session()->flash('success', 'Nota guardada.');
    }

    private function updateReport(int $reportId, string $status): void
    {
        SocialReport::where('id', $reportId)->update([
            'status'     => $status,
            'admin_note' => $this->adminNote ?: null,
        ]);
        $this->closeReport();
        session()->flash('success', 'Denúncia atualizada.');
    }
}
