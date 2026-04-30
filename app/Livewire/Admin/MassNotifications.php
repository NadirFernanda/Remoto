<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Modules\Admin\Services\AuditLogger;

class MassNotifications extends Component
{
    public string $target    = 'all';   // all | freelancers | clients | user
    public string $titulo    = '';
    public string $mensagem  = '';
    public bool   $sent      = false;
    public string $errorMsg  = '';
    public string $userQuery = '';
    public ?int $userId = null;
    public array $userMatches = [];

    protected array $rules = [
        'target'   => 'required|in:all,freelancers,clients,user',
        'titulo'   => 'required|string|max:120',
        'mensagem' => 'required|string|max:1000',
    ];

    protected array $messages = [
        'titulo.required'   => 'O título é obrigatório.',
        'mensagem.required' => 'A mensagem é obrigatória.',
        'mensagem.max'      => 'A mensagem não pode ter mais de 1000 caracteres.',
    ];

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
        // Aceitar ?target= da URL (via header Notificação dropdown)
        $t = request()->query('target', '');
        if (in_array($t, ['all', 'freelancers', 'clients', 'user'], true)) {
            $this->target = $t;
        }
    }

    public function send(): void
    {
        $this->validate();

        if ($this->target === 'user' && !$this->userId) {
            $this->errorMsg = 'Selecione um utilizador para enviar a notificação.';
            return;
        }

        $query = User::where('status', 'active');
        if ($this->target === 'freelancers') {
            $query->where('role', 'freelancer');
        } elseif ($this->target === 'clients') {
            $query->where('role', 'cliente');
        } elseif ($this->target === 'user') {
            $query->where('id', $this->userId);
        }

        $users = $query->get();
        $count = 0;

        foreach ($users as $user) {
            \App\Models\Notification::create([
                'user_id'     => $user->id,
                'type'        => 'admin_message',
                'title'       => $this->titulo,
                'message'     => $this->mensagem,
                'sender_name' => auth()->user()->name,
                'read'        => false,
            ]);
            $count++;
        }

        AuditLogger::log('mass_notification', "Notificação enviada para {$count} utilizadores (target: {$this->target})");

        $this->reset(['titulo', 'mensagem', 'target', 'userQuery', 'userId', 'userMatches']);
        $this->sent = true;
    }

    public function updatedUserQuery(): void
    {
        $q = trim($this->userQuery);
        if ($q === '') {
            $this->userMatches = [];
            return;
        }

        $this->userMatches = User::where('status', 'active')
            ->where(function ($query) use ($q) {
                $query->where('name', 'ilike', '%' . $q . '%')
                    ->orWhere('email', 'ilike', '%' . $q . '%');
                // Pesquisa por ID apenas se for numérico (evita erro de tipo no PostgreSQL)
                if (is_numeric($q)) {
                    $query->orWhere('id', (int) $q);
                }
            })
            ->limit(8)
            ->get(['id', 'name', 'email', 'role'])
            ->toArray();
    }

    public function selectUser(int $id): void
    {
        $user = User::find($id);
        if (!$user) {
            return;
        }

        $this->userId = $user->id;
        $this->userQuery = $user->name . ' — ' . $user->email;
        $this->userMatches = [];
    }

    public function render()
    {
        return view('livewire.admin.mass-notifications')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Notificações em Massa']);
    }
}
