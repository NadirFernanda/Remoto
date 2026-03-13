<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Modules\Admin\Services\AuditLogger;

class MassNotifications extends Component
{
    public string $target    = 'all';   // all | freelancers | clients
    public string $titulo    = '';
    public string $mensagem  = '';
    public bool   $sent      = false;
    public string $errorMsg  = '';

    protected array $rules = [
        'target'   => 'required|in:all,freelancers,clients',
        'titulo'   => 'required|string|max:120',
        'mensagem' => 'required|string|max:1000',
    ];

    protected array $messages = [
        'titulo.required'   => 'O título é obrigatório.',
        'mensagem.required' => 'A mensagem é obrigatória.',
        'mensagem.max'      => 'A mensagem não pode ter mais de 1000 caracteres.',
    ];

    public function send(): void
    {
        $this->validate();

        $query = User::where('status', 'active');
        if ($this->target === 'freelancers') {
            $query->where('role', 'freelancer');
        } elseif ($this->target === 'clients') {
            $query->where('role', 'cliente');
        }

        $users = $query->get();
        $count = 0;

        foreach ($users as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title'   => $this->titulo,
                'message' => $this->mensagem,
                'read'    => false,
            ]);
            $count++;
        }

        AuditLogger::log('mass_notification', "Notificação em massa enviada para {$count} utilizadores (target: {$this->target})");

        $this->reset(['titulo', 'mensagem', 'target']);
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.admin.mass-notifications')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Notificações em Massa']);
    }
}
