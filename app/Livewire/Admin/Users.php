<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Services\AuditLogger;

class Users extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $roleFilter  = '';
    public string $kycFilter   = '';

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
        AuditLogger::log('kyc_verified', "KYC verificado para {$user->name} ({$user->email})", 'User', $id, $before, ['kyc_status' => 'verified']);
        session()->flash('success', 'KYC verificado.');
    }

    public function rejectKyc(int $id): void
    {
        $user   = User::findOrFail($id);
        $before = ['kyc_status' => $user->kyc_status];
        $user->update(['kyc_status' => 'rejected']);
        AuditLogger::log('kyc_rejected', "KYC rejeitado para {$user->name} ({$user->email})", 'User', $id, $before, ['kyc_status' => 'rejected']);
        session()->flash('success', 'KYC rejeitado.');
    }

    public function bulkVerifyKyc(): void
    {
        $count = User::where('kyc_status', 'pending')->where('role', '!=', 'admin')->update(['kyc_status' => 'verified']);
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

        return view('livewire.admin.users', compact('users', 'pendingKyc'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão de Utilizadores']);
    }
}

