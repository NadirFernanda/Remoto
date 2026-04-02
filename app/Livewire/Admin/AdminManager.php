<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\AdminPermission;
use App\Models\AdminSecurity;
use App\Models\AdminNotificationPreference;
use App\Modules\Admin\Services\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManager extends Component
{
    use WithPagination;

    // ── List filters ──────────────────────────────────────────────────────────
    public string $search     = '';
    public string $roleFilter = '';

    // ── Modal state ───────────────────────────────────────────────────────────
    public string $modalMode = ''; // create | edit | permissions | security | ''
    public ?int $editingId   = null;

    // ── Profile fields ────────────────────────────────────────────────────────
    public string $name             = '';
    public string $email            = '';
    public string $corporateEmail   = '';
    public string $phone            = '';
    public string $cargo            = '';
    public string $adminRole        = 'gestor';
    public string $password         = '';
    public string $passwordConfirm  = '';

    // ── Permissions (keyed by module => access level) ─────────────────────────
    public array $permissions = [];

    // ── Security ──────────────────────────────────────────────────────────────
    public bool   $twoFactorRequired        = false;
    public bool   $ipRestriction            = false;
    public string $allowedIps               = '';
    public bool   $sessionTimeoutEnabled    = true;
    public int    $sessionTimeoutMinutes    = 60;
    public bool   $forcePasswordChange      = false;

    // ── Notifications ─────────────────────────────────────────────────────────
    public bool   $notifyNewUser               = true;
    public bool   $notifyNewDispute            = true;
    public bool   $notifyKycPending            = true;
    public bool   $notifyPayoutRequest         = true;
    public bool   $notifyHighValueTransaction  = false;
    public bool   $notifySystemError           = true;
    public bool   $notifyDailyReport           = false;
    public string $notifyChannel               = 'both';

    // ── Active permissions tab ────────────────────────────────────────────────
    public string $permTab = 'perfil'; // perfil | permissoes | seguranca | notificacoes

    public function mount(): void
    {
        $user = auth()->user();
        abort_if($user?->role !== 'admin', 403);
        abort_if($user->admin_role !== 'master' && $user->admin_role !== null, 403,
            'Apenas o Admin Master pode gerir administradores.');
    }

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingRoleFilter(): void { $this->resetPage(); }

    // Quando o perfil de acesso muda, recarregar permissões padrão automaticamente
    public function updatedAdminRole(string $value): void
    {
        $this->loadDefaultPermissions($value);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPEN MODALS
    // ─────────────────────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->loadDefaultPermissions('gestor');
        $this->modalMode = 'create';
        $this->permTab   = 'perfil';
    }

    public function openEdit(int $id): void
    {
        $admin = User::findOrFail($id);
        $this->guardNotSelf($id);

        $this->editingId       = $id;
        $this->name            = $admin->name;
        $this->email           = $admin->email;
        $this->corporateEmail  = $admin->admin_corporate_email ?? '';
        $this->phone           = $admin->admin_phone ?? '';
        $this->cargo           = $admin->admin_cargo ?? '';
        $this->adminRole       = $admin->admin_role ?? 'master';
        $this->password        = '';
        $this->passwordConfirm = '';

        $this->loadPermissionsFromDb($id);
        $this->loadSecurityFromDb($id);
        $this->loadNotificationsFromDb($id);

        $this->modalMode = 'edit';
        $this->permTab   = 'perfil';
    }

    public function closeModal(): void
    {
        $this->modalMode = '';
        $this->editingId = null;
        $this->resetForm();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SAVE
    // ─────────────────────────────────────────────────────────────────────────

    public function saveAdmin(): void
    {
        if ($this->modalMode === 'create') {
            $this->createAdmin();
        } else {
            $this->updateAdmin();
        }
    }

    private function createAdmin(): void
    {
        $this->permTab = 'perfil'; // navegar para o tab com os campos obrigatórios antes de validar

        $this->validate([
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:users,email',
            'corporateEmail'  => 'nullable|email|max:150',
            'phone'           => 'nullable|string|max:30',
            'cargo'           => 'nullable|string|max:100',
            'adminRole'       => ['required', Rule::in(['master', 'financeiro', 'gestor', 'suporte', 'analista'])],
            'password'        => 'required|string|min:10|same:passwordConfirm',
        ], [], [
            'name'           => 'Nome',
            'email'          => 'E-mail de login',
            'password'       => 'Senha',
            'passwordConfirm'=> 'Confirmação de senha',
        ]);

        // Only master can create another master
        if ($this->adminRole === 'master') {
            $existingMasters = User::where('role', 'admin')
                ->where(fn($q) => $q->whereNull('admin_role')->orWhere('admin_role', 'master'))
                ->count();
            if ($existingMasters > 0) {
                $this->addError('adminRole', 'Já existe um Admin Master no sistema.');
                return;
            }
        }

        DB::transaction(function () {
            $admin = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $admin->role                   = 'admin';
            $admin->admin_role             = $this->adminRole === 'master' ? null : $this->adminRole;
            $admin->admin_corporate_email  = $this->corporateEmail ?: null;
            $admin->admin_phone            = $this->phone ?: null;
            $admin->admin_cargo            = $this->cargo ?: null;
            $admin->email_verified_at      = now();
            $admin->save();

            $this->savePermissions($admin->id);
            $this->saveSecurity($admin->id);
            $this->saveNotifications($admin->id);

            AuditLogger::log(
                'admin_created',
                "Novo administrador criado: {$admin->name} ({$admin->email}), cargo: {$this->cargo}, papel: {$this->adminRole}",
                'User',
                $admin->id,
                category: 'operacoes'
            );
        });

        $createdName = $this->name;
        $this->closeModal();
        session()->flash('success', "Administrador {$createdName} criado com sucesso.");
    }

    private function updateAdmin(): void
    {
        $admin = User::findOrFail($this->editingId);
        $this->guardNotSelf($this->editingId);

        $this->permTab = 'perfil'; // navegar para o tab com os campos obrigatórios antes de validar

        $this->validate([
            'name'           => 'required|string|max:100',
            'email'          => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingId)],
            'corporateEmail' => 'nullable|email|max:150',
            'phone'          => 'nullable|string|max:30',
            'cargo'          => 'nullable|string|max:100',
            'adminRole'      => ['required', Rule::in(['master', 'financeiro', 'gestor', 'suporte', 'analista'])],
            'password'       => 'nullable|string|min:10|same:passwordConfirm',
        ]);

        $before = $admin->only(['name', 'email', 'admin_role', 'admin_cargo']);

        DB::transaction(function () use ($admin, $before) {
            $admin->name                  = $this->name;
            $admin->email                 = $this->email;
            $admin->admin_corporate_email = $this->corporateEmail ?: null;
            $admin->admin_phone           = $this->phone ?: null;
            $admin->admin_cargo           = $this->cargo ?: null;
            $admin->admin_role            = $this->adminRole === 'master' ? null : $this->adminRole;
            if ($this->password) {
                $admin->password = Hash::make($this->password);
            }
            $admin->save();

            $this->savePermissions($admin->id);
            $this->saveSecurity($admin->id);
            $this->saveNotifications($admin->id);

            AuditLogger::log(
                'admin_updated',
                "Administrador actualizado: {$admin->name} ({$admin->email})",
                'User',
                $admin->id,
                $before,
                $admin->only(['name', 'email', 'admin_role', 'admin_cargo']),
                category: 'operacoes'
            );
        });

        $updatedName = $this->name;
        $this->closeModal();
        session()->flash('success', "Administrador {$updatedName} actualizado.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PERMISSIONS
    // ─────────────────────────────────────────────────────────────────────────

    public function applyRoleDefaults(): void
    {
        $this->loadDefaultPermissions($this->adminRole);
    }

    private function loadDefaultPermissions(string $role): void
    {
        $defaults = AdminPermission::ROLE_DEFAULTS[$role] ?? [];
        $this->permissions = [];
        foreach (AdminPermission::MODULES as $key => $label) {
            $this->permissions[$key] = $defaults[$key] ?? 'none';
        }
    }

    private function loadPermissionsFromDb(int $userId): void
    {
        $saved = AdminPermission::where('user_id', $userId)
            ->pluck('access', 'module')
            ->toArray();

        $this->permissions = [];
        foreach (AdminPermission::MODULES as $key => $label) {
            $this->permissions[$key] = $saved[$key] ?? 'none';
        }
    }

    private function savePermissions(int $userId): void
    {
        // Master always has full — don't persist for master
        $role = $this->adminRole;
        if ($role === 'master') {
            AdminPermission::where('user_id', $userId)->delete();
            return;
        }

        foreach ($this->permissions as $module => $access) {
            AdminPermission::updateOrCreate(
                ['user_id' => $userId, 'module' => $module],
                ['access' => $access]
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SECURITY
    // ─────────────────────────────────────────────────────────────────────────

    private function loadSecurityFromDb(int $userId): void
    {
        $sec = AdminSecurity::firstOrNew(['user_id' => $userId]);
        $this->twoFactorRequired       = (bool) $sec->two_factor_required;
        $this->ipRestriction           = (bool) $sec->ip_restriction;
        $this->allowedIps              = $sec->allowed_ips
            ? implode("\n", json_decode($sec->allowed_ips, true) ?? [])
            : '';
        $this->sessionTimeoutEnabled   = $sec->exists ? (bool) $sec->session_timeout_enabled : true;
        $this->sessionTimeoutMinutes   = $sec->session_timeout_minutes ?? 60;
        $this->forcePasswordChange     = (bool) $sec->force_password_change;
    }

    private function saveSecurity(int $userId): void
    {
        $ips = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $this->allowedIps) ?? []));

        AdminSecurity::updateOrCreate(
            ['user_id' => $userId],
            [
                'two_factor_required'      => $this->twoFactorRequired,
                'ip_restriction'           => $this->ipRestriction,
                'allowed_ips'              => count($ips) ? json_encode(array_values($ips)) : null,
                'session_timeout_enabled'  => $this->sessionTimeoutEnabled,
                'session_timeout_minutes'  => $this->sessionTimeoutMinutes,
                'force_password_change'    => $this->forcePasswordChange,
            ]
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // NOTIFICATIONS
    // ─────────────────────────────────────────────────────────────────────────

    private function loadNotificationsFromDb(int $userId): void
    {
        $n = AdminNotificationPreference::firstOrNew(['user_id' => $userId]);
        $this->notifyNewUser              = $n->notify_new_user ?? true;
        $this->notifyNewDispute           = $n->notify_new_dispute ?? true;
        $this->notifyKycPending           = $n->notify_kyc_pending ?? true;
        $this->notifyPayoutRequest        = $n->notify_payout_request ?? true;
        $this->notifyHighValueTransaction = $n->notify_high_value_transaction ?? false;
        $this->notifySystemError          = $n->notify_system_error ?? true;
        $this->notifyDailyReport          = $n->notify_daily_report ?? false;
        $this->notifyChannel              = $n->channel ?? 'both';
    }

    private function saveNotifications(int $userId): void
    {
        AdminNotificationPreference::updateOrCreate(
            ['user_id' => $userId],
            [
                'notify_new_user'               => $this->notifyNewUser,
                'notify_new_dispute'            => $this->notifyNewDispute,
                'notify_kyc_pending'            => $this->notifyKycPending,
                'notify_payout_request'         => $this->notifyPayoutRequest,
                'notify_high_value_transaction' => $this->notifyHighValueTransaction,
                'notify_system_error'           => $this->notifySystemError,
                'notify_daily_report'           => $this->notifyDailyReport,
                'channel'                       => $this->notifyChannel,
            ]
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────────────────────────────────

    public function deleteAdmin(int $id): void
    {
        $this->guardNotSelf($id);
        $admin = User::findOrFail($id);

        if ($admin->admin_role === null || $admin->admin_role === 'master') {
            session()->flash('error', 'Não é possível remover o Admin Master principal.');
            return;
        }

        AuditLogger::log(
            'admin_deleted',
            "Administrador removido: {$admin->name} ({$admin->email})",
            'User',
            $id,
            category: 'operacoes'
        );

        // Remove profile data
        AdminPermission::where('user_id', $id)->delete();
        AdminSecurity::where('user_id', $id)->delete();
        AdminNotificationPreference::where('user_id', $id)->delete();

        // Convert to suspended non-admin instead of hard delete (audit trail preservation)
        $admin->role        = 'cliente';
        $admin->admin_role  = null;
        $admin->is_suspended = true;
        $admin->save();

        session()->flash('success', "Administrador {$admin->name} removido do painel.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function guardNotSelf(int $id): void
    {
        if ($id === auth()->id()) {
            abort(403, 'Não pode editar a sua própria conta nesta secção.');
        }
    }

    private function resetForm(): void
    {
        $this->editingId       = null;
        $this->name            = '';
        $this->email           = '';
        $this->corporateEmail  = '';
        $this->phone           = '';
        $this->cargo           = '';
        $this->adminRole       = 'gestor';
        $this->password        = '';
        $this->passwordConfirm = '';
        $this->permissions     = [];
        $this->allowedIps      = '';
        $this->twoFactorRequired       = false;
        $this->ipRestriction           = false;
        $this->sessionTimeoutEnabled   = true;
        $this->sessionTimeoutMinutes   = 60;
        $this->forcePasswordChange     = false;
        $this->notifyNewUser              = true;
        $this->notifyNewDispute           = true;
        $this->notifyKycPending           = true;
        $this->notifyPayoutRequest        = true;
        $this->notifyHighValueTransaction = false;
        $this->notifySystemError          = true;
        $this->notifyDailyReport          = false;
        $this->notifyChannel              = 'both';
        $this->resetErrorBag();
    }

    public function render()
    {
        $admins = User::where('role', 'admin')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name',  'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('admin_corporate_email', 'like', "%{$this->search}%")
                  ->orWhere('admin_cargo', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn($q) => $q->where('admin_role', $this->roleFilter === 'master' ? null : $this->roleFilter))
            ->with(['adminPermissions', 'adminSecurity'])
            ->orderByRaw("CASE WHEN admin_role IS NULL THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate(15);

        $modules = AdminPermission::MODULES;

        return view('livewire.admin.admin-manager', compact('admins', 'modules'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão de Administradores']);
    }
}
