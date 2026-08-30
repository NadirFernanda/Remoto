<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\KycSubmission;
use App\Modules\Admin\Services\AuditLogger;
use App\Events\KycStatusChanged;
use Illuminate\Support\Facades\Auth;

class Users extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $roleFilter  = '';
    public string $kycFilter   = '';

    // Selecção em lote
    public array $selected    = [];
    public bool  $selectPage  = false;

    // KYC review modal
    public ?int $reviewingSubmissionId = null;
    public string $adminNotes = '';
    public ?string $noDocsUserName = null;
    public string $verifiedName = '';
    public bool $verifiedNameFromOcr = false;
    public string $documentNumber = '';
    public bool $documentNumberFromOcr = false;
    public ?string $ocrRawText = null;

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function updatingSearch(): void    { $this->resetPage(); $this->clearSelection(); }
    public function updatingRoleFilter(): void { $this->resetPage(); $this->clearSelection(); }
    public function updatingKycFilter(): void  { $this->resetPage(); $this->clearSelection(); }

    private function clearSelection(): void
    {
        $this->selected   = [];
        $this->selectPage = false;
    }

    public function approveUser(int $id): void
    {
        $user = User::findOrFail($id);
        $user->status       = 'active';
        $user->is_suspended = false;
        $user->save();
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
        $user->is_suspended = true;
        $user->status       = 'suspended';
        $user->save();
        AuditLogger::log('user_suspended', "Utilizador {$user->name} ({$user->email}) suspenso", 'User', $id);
        session()->flash('success', 'Utilizador suspenso.');
    }

    public function verifyKyc(int $id): void
    {
        $user   = User::findOrFail($id);
        $before = ['kyc_status' => $user->kyc_status];
        $user->kyc_status = 'verified';
        $user->kyc_verified_name = $user->name;
        $user->save();
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
        $user->kyc_status = 'rejected';
        $user->save();
        KycSubmission::where('user_id', $id)->where('status', 'pending')
            ->update(['status' => 'rejected', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]);
        AuditLogger::log('kyc_rejected', "KYC rejeitado para {$user->name} ({$user->email})", 'User', $id, $before, ['kyc_status' => 'rejected']);
        KycStatusChanged::dispatch($user, 'rejected');
        session()->flash('success', 'KYC rejeitado.');
    }

    public function reviewUserKyc(int $userId): void
    {
        $submission = KycSubmission::where('user_id', $userId)->latest()->first();
        if ($submission) {
            $this->reviewingSubmissionId = $submission->id;
            $this->adminNotes = '';
            $this->noDocsUserName = null;
            $this->prefillVerifiedNameFromDocument($submission);
        } else {
            $user = User::findOrFail($userId);
            $this->noDocsUserName = strip_tags($user->name);
            $this->reviewingSubmissionId = null;
        }
    }

    public function openKycReview(int $submissionId): void
    {
        $submission = KycSubmission::findOrFail($submissionId);
        $this->reviewingSubmissionId = $submissionId;
        $this->adminNotes = '';
        $this->noDocsUserName = null;
        $this->prefillVerifiedNameFromDocument($submission);
    }

    /**
     * Pré-preenche os campos "Nome completo" e "Número do BI" com sugestões
     * extraídas da frente do documento via Google Vision (ver
     * KycVisionService). Se a chave da API não estiver configurada ou a
     * extracção falhar, cai sempre no comportamento anterior (nome copiado
     * do perfil, número em branco) — o admin confirma/corrige antes de
     * aprovar em qualquer dos casos.
     */
    private function prefillVerifiedNameFromDocument(KycSubmission $submission): void
    {
        $ocr = app(\App\Services\KycVisionService::class)
            ->extractFromDocument($submission->id, $submission->document_front_path);

        $this->ocrRawText = $ocr['raw_text'];
        $this->verifiedNameFromOcr = (bool) $ocr['name'];
        $this->verifiedName = $ocr['name'] ?? $submission->user->name;
        $this->documentNumberFromOcr = (bool) $ocr['document_number'];
        $this->documentNumber = $ocr['document_number'] ?? ($submission->document_number ?? '');
    }

    public function closeKycReview(): void
    {
        $this->reviewingSubmissionId = null;
        $this->adminNotes = '';
        $this->noDocsUserName = null;
        $this->verifiedName = '';
        $this->verifiedNameFromOcr = false;
        $this->documentNumber = '';
        $this->documentNumberFromOcr = false;
        $this->ocrRawText = null;
    }

    public function approveKycSubmission(): void
    {
        // "nullable" só é ignorado pela validação quando o valor é null — um
        // texto vazio ('', o que o input envia quando fica em branco) ainda
        // seria verificado contra a restrição de unicidade. Por isso só
        // aplicamos a regra "unique" quando o admin realmente preencheu algo.
        $rules = ['verifiedName' => 'required|string|max:255'];
        if (trim($this->documentNumber) !== '') {
            $rules['documentNumber'] = 'string|max:50|unique:kyc_submissions,document_number,' . $this->reviewingSubmissionId;
        }

        $this->validate($rules, [], [
            'verifiedName'   => 'nome completo (conforme o documento)',
            'documentNumber' => 'número do documento',
        ]);

        $submission = KycSubmission::findOrFail($this->reviewingSubmissionId);
        $submission->update([
            'status'          => 'approved',
            'admin_notes'     => $this->adminNotes ?: null,
            'document_number' => $this->documentNumber !== '' ? $this->documentNumber : null,
            'reviewed_by'     => Auth::id(),
            'reviewed_at'     => now(),
        ]);
        $submission->user->kyc_status = 'verified';
        $submission->user->kyc_verified_name = strip_tags($this->verifiedName);
        $submission->user->save();
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
        $submission->user->kyc_status = 'rejected';
        $submission->user->save();
        AuditLogger::log('kyc_rejected', "KYC rejeitado para {$submission->user->name}", 'User', $submission->user_id);
        KycStatusChanged::dispatch($submission->user, 'rejected', $this->adminNotes ?: null);
        $this->closeKycReview();
        session()->flash('success', 'KYC rejeitado.');
    }

    public function setAdminRole(int $userId, string $role): void
    {
        // Sem esta verificação, qualquer admin (ex: "gestor") conseguia
        // chamar este método em si próprio e tornar-se Master — falha
        // crítica de escalonamento de privilégio encontrada em auditoria.
        abort_if(auth()->user()->admin_role !== null, 403, 'Apenas o Admin Master pode alterar níveis de acesso.');

        $allowed = ['master', 'gestor', 'financeiro', 'suporte', 'analista', ''];
        if (! in_array($role, $allowed, true)) {
            return;
        }
        $user = User::findOrFail($userId);
        if ($user->role !== 'admin') {
            return;
        }
        $user->admin_role = $role ?: null;
        $user->save();
        AuditLogger::log('admin_role_changed', "Nível de acesso de {$user->name} alterado para: " . ($role ?: 'master (padrão)'), 'User', $userId);
        session()->flash('success', 'Nível de acesso actualizado.');
    }

    /**
     * Elimina permanentemente um utilizador — só o Admin Master, e só quando
     * a conta não tem nenhuma actividade real (saldo, projectos, assinaturas)
     * associada, para servir para limpar contas de teste sem risco de apagar
     * dados financeiros reais por engano. Contas com actividade devem ser
     * suspensas, nunca eliminadas.
     */
    public function deleteUser(int $id, bool $force = false): void
    {
        abort_if(auth()->user()->admin_role !== null, 403, 'Apenas o Admin Master pode eliminar utilizadores.');

        $user = User::findOrFail($id);
        $error = $this->tryDeleteUser($user, $force);

        if ($error) {
            session()->flash('error', $error);
            return;
        }

        session()->flash('success', "Utilizador \"{$user->name}\" eliminado." . ($force ? ' (eliminação forçada)' : ''));
    }

    /**
     * Tenta eliminar um utilizador; devolve null em caso de sucesso, ou uma
     * mensagem de erro se a conta não puder ser eliminada. Partilhado entre
     * a eliminação individual e a eliminação em lote.
     *
     * $force ignora a rede de segurança de actividade (saldo/projectos/
     * assinaturas) — usar só quando se tem a certeza de que a conta não tem
     * nada de real, porque apaga tudo de forma permanente e irreversível.
     */
    private function tryDeleteUser(User $user, bool $force = false): ?string
    {
        if ($user->id === auth()->id()) {
            return 'Não pode eliminar a sua própria conta.';
        }

        if ($user->role === 'admin') {
            return 'Não é possível eliminar contas de administrador por aqui.';
        }

        if (!$force) {
            $wallet = $user->wallet;
            $hasWalletActivity = $wallet && ((float) ($wallet->saldo ?? 0) > 0 || (float) ($wallet->saldo_pendente ?? 0) > 0);

            $hasServiceActivity = \App\Models\Service::where('cliente_id', $user->id)
                ->orWhere('freelancer_id', $user->id)
                ->whereNotIn('status', ['draft'])
                ->exists();

            $hasSubscriptionActivity = \App\Models\CreatorSubscription::where('subscriber_id', $user->id)
                ->orWhere('creator_id', $user->id)
                ->exists();

            if ($hasWalletActivity || $hasServiceActivity || $hasSubscriptionActivity) {
                return 'Tem saldo, projectos ou assinaturas associadas.';
            }
        }

        $name  = $user->name;
        $email = $user->email;

        AuditLogger::log(
            $force ? 'user_force_deleted' : 'user_deleted',
            "Utilizador {$name} ({$email}) eliminado permanentemente" . ($force ? ' — FORÇADO, ignorando saldo/projectos/assinaturas' : ''),
            'User',
            $user->id
        );
        $user->delete();

        return null;
    }

    /** Elimina, em lote, os utilizadores marcados na tabela (mesmas regras de segurança do individual). */
    public function bulkDeleteSelected(bool $force = false): void
    {
        abort_if(auth()->user()->admin_role !== null, 403, 'Apenas o Admin Master pode eliminar utilizadores.');

        if (empty($this->selected)) {
            return;
        }

        $eliminados = 0;
        $ignorados  = 0;

        foreach (User::whereIn('id', $this->selected)->get() as $user) {
            if ($this->tryDeleteUser($user, $force) === null) {
                $eliminados++;
            } else {
                $ignorados++;
            }
        }

        $this->clearSelection();

        $msg = "{$eliminados} utilizador(es) eliminado(s)." . ($force && $eliminados > 0 ? ' (eliminação forçada)' : '');
        if ($ignorados > 0) {
            $msg .= " {$ignorados} ignorado(s)" . ($force ? ' (conta de administrador ou a sua própria).' : ' por terem saldo, projectos ou assinaturas associadas.');
        }
        session()->flash($eliminados > 0 ? 'success' : 'error', $msg);
    }

    /** Suspende, em lote, os utilizadores marcados na tabela. */
    public function bulkSuspendSelected(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $users = User::whereIn('id', $this->selected)->where('role', '!=', 'admin')->get();

        foreach ($users as $user) {
            $user->is_suspended = true;
            $user->status       = 'suspended';
            $user->save();
        }

        AuditLogger::log('user_bulk_suspended', $users->count() . ' utilizador(es) suspenso(s) em lote', 'User', null);

        $this->clearSelection();

        session()->flash('success', $users->count() . ' utilizador(es) suspenso(s).');
    }

    public function updatedSelectPage(bool $value): void
    {
        $pageIds = $this->usersOnPage()->pluck('id')
            ->filter(fn ($id) => $id !== auth()->id())
            ->values()
            ->all();

        $this->selected = $value
            ? array_values(array_unique(array_merge($this->selected, $pageIds)))
            : array_values(array_diff($this->selected, $pageIds));
    }

    public function bulkVerifyKyc(): void
    {
        $count = User::where('kyc_status', 'pending')->where('role', '!=', 'admin')
            ->update(['kyc_status' => 'verified', 'kyc_verified_name' => \Illuminate\Support\Facades\DB::raw('name')]);
        KycSubmission::where('status', 'pending')->update(['status' => 'approved', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]);
        AuditLogger::log('kyc_bulk_verified', "KYC em lote: {$count} utilizadores verificados", 'User', null);
        session()->flash('success', "{$count} utilizadores verificados em lote.");
    }

    /** Mesma query/paginação usada por render() — reutilizada para "seleccionar tudo nesta página". */
    private function usersOnPage()
    {
        return User::query()
            ->where('role', '!=', 'admin')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->when($this->kycFilter,  fn($q) => $q->where('kyc_status', $this->kycFilter))
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function render()
    {
        $users = $this->usersOnPage();

        $pendingKyc = User::where('kyc_status', 'pending')->where('role', '!=', 'admin')->count();
        $pendingSubmissions = KycSubmission::with('user')->where('status', 'pending')->latest()->get();
        // Latest submission per user (any status) for the "Ver docs" button
        $allSubmissionsByUser = KycSubmission::whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')->from('kyc_submissions')->groupBy('user_id');
            })
            ->get()
            ->keyBy('user_id');
        $reviewingSubmission = $this->reviewingSubmissionId
            ? KycSubmission::with('user')->find($this->reviewingSubmissionId)
            : null;

        return view('livewire.admin.users', compact('users', 'pendingKyc', 'pendingSubmissions', 'allSubmissionsByUser', 'reviewingSubmission'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão de Utilizadores']);
    }
}

