<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\KycSubmission;
use App\Modules\Admin\Services\AuditLogger;
use App\Events\KycStatusChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Users extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $roleFilter  = '';
    public string $kycFilter   = '';

    // KYC review modal
    public ?int $reviewingSubmissionId = null;
    public string $adminNotes = '';

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingRoleFilter(): void { $this->resetPage(); }
    public function updatingKycFilter(): void  { $this->resetPage(); }

    public function approveUser(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active', 'is_suspended' => false]);
        AuditLogger::log('user_approved', "Utilizador {$user->name} ({$user->email}) aprovado/reactivado", 'User', $id);
        session()->flash('success', 'Utilizador aprovado.');
    }

    public function suspendUser(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
            session()->flash('error', 'Não é possível suspender um admin.');
            return;
        }
        $user->update(['is_suspended' => true, 'status' => 'suspended']);
        AuditLogger::log('user_suspended', "Utilizador {$user->name} ({$user->email}) suspenso", 'User', $id);
        session()->flash('success', 'Utilizador suspenso.');
    }

    public function verifyKyc(int $id): void
    {
        $user   = User::findOrFail($id);
        $before = ['kyc_status' => $user->kyc_status];
        $user->update(['kyc_status' => 'verified']);
        // Also approve pending submission if exists
        KycSubmission::where('user_id', $id)->where('status', 'pending')
            ->update(['status' => 'approved', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]);
        AuditLogger::log('kyc_verified', "KYC verificado para {$user->name} ({$user->email})", 'User', $id, $before, ['kyc_status' => 'verified']);
        KycStatusChanged::dispatch($user, 'verified');
        session()->flash('success', 'KYC verificado.');
    }

    public function rejectKyc(int $id): void
    {
        $user   = User::findOrFail($id);
        $before = ['kyc_status' => $user->kyc_status];
        $user->update(['kyc_status' => 'rejected']);
        KycSubmission::where('user_id', $id)->where('status', 'pending')
            ->update(['status' => 'rejected', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]);
        AuditLogger::log('kyc_rejected', "KYC rejeitado para {$user->name} ({$user->email})", 'User', $id, $before, ['kyc_status' => 'rejected']);
        KycStatusChanged::dispatch($user, 'rejected');
        session()->flash('success', 'KYC rejeitado.');
    }

    public function openKycReview(int $submissionId): void
    {
        $this->reviewingSubmissionId = $submissionId;
        $this->adminNotes = '';
    }

    public function closeKycReview(): void
    {
        $this->reviewingSubmissionId = null;
        $this->adminNotes = '';
    }

    public function approveKycSubmission(): void
    {
        $submission = KycSubmission::findOrFail($this->reviewingSubmissionId);
        $submission->update([
            'status'      => 'approved',
            'admin_notes' => $this->adminNotes ?: null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
        $submission->user->update(['kyc_status' => 'verified']);
        AuditLogger::log('kyc_verified', "KYC aprovado para {$submission->user->name}", 'User', $submission->user_id);
        KycStatusChanged::dispatch($submission->user, 'verified', $this->adminNotes ?: null);
        $this->closeKycReview();
        session()->flash('success', 'KYC aprovado com sucesso.');
    }

    public function rejectKycSubmission(): void
    {
        $submission = KycSubmission::findOrFail($this->reviewingSubmissionId);
        $submission->update([
            'status'      => 'rejected',
            'admin_notes' => $this->adminNotes ?: null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
        $submission->user->update(['kyc_status' => 'rejected']);
        AuditLogger::log('kyc_rejected', "KYC rejeitado para {$submission->user->name}", 'User', $submission->user_id);
        KycStatusChanged::dispatch($submission->user, 'rejected', $this->adminNotes ?: null);
        $this->closeKycReview();
        session()->flash('success', 'KYC rejeitado.');
    }

    public function kycDocumentUrl(string $path): string
    {
        return Storage::disk('private')->url($path);
    }

    public function setAdminRole(int $userId, string $role): void
    {
        $allowed = ['master', 'gestor', 'financeiro', ''];
        if (! in_array($role, $allowed, true)) {
            return;
        }
        $user = User::findOrFail($userId);
        if ($user->role !== 'admin') {
            return;
        }
        $user->update(['admin_role' => $role ?: null]);
        AuditLogger::log('admin_role_changed', "Nível de acesso de {$user->name} alterado para: " . ($role ?: 'master (padrão)'), 'User', $userId);
        session()->flash('success', 'Nível de acesso actualizado.');
    }

    public function bulkVerifyKyc(): void
    {
        $count = User::where('kyc_status', 'pending')->where('role', '!=', 'admin')->update(['kyc_status' => 'verified']);
        KycSubmission::where('status', 'pending')->update(['status' => 'approved', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]);
        AuditLogger::log('kyc_bulk_verified', "KYC em lote: {$count} utilizadores verificados", 'User', null);
        session()->flash('success', "{$count} utilizadores verificados em lote.");
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->when($this->kycFilter,  fn($q) => $q->where('kyc_status', $this->kycFilter))
            ->orderByDesc('created_at')
            ->paginate(20);

        $pendingKyc = User::where('kyc_status', 'pending')->where('role', '!=', 'admin')->count();
        $pendingSubmissions = KycSubmission::with('user')->where('status', 'pending')->latest()->get();
        $reviewingSubmission = $this->reviewingSubmissionId
            ? KycSubmission::with('user')->find($this->reviewingSubmissionId)
            : null;

        return view('livewire.admin.users', compact('users', 'pendingKyc', 'pendingSubmissions', 'reviewingSubmission'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão de Utilizadores']);
    }
}

