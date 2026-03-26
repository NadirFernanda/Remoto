<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class Settings extends Component
{
    public $user;
    public $notify_new_project_email;
    public string $deactivatePassword = '';
    public string $deletePassword = '';
    public string $deleteConfirmation = '';

    public function mount()
    {
        $this->user = Auth::user();
        $this->notify_new_project_email = $this->user->notify_new_project_email;
    }

    public function updatedNotifyNewProjectEmail($value)
    {
        $this->user->notify_new_project_email = $value;
        $this->user->save();
        session()->flash('success', 'Preferência de notificação atualizada com sucesso!');
    }

    public function deactivateAccount()
    {
        $this->validate([
            'deactivatePassword' => 'required|string|min:6',
        ]);

        if (!Hash::check($this->deactivatePassword, (string) $this->user->password)) {
            $this->addError('deactivatePassword', 'Palavra-passe inválida.');
            return;
        }

        $this->user->status = 'suspended';
        $this->user->is_suspended = true;
        $this->user->save();

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/')->with('success', 'Conta desativada com sucesso.');
    }

    public function deleteAccount()
    {
        $this->validate([
            'deletePassword' => 'required|string|min:6',
            'deleteConfirmation' => 'required|in:REMOVER',
        ], [
            'deleteConfirmation.in' => 'Digite REMOVER para confirmar a eliminação da conta.',
        ]);

        if (!Hash::check($this->deletePassword, (string) $this->user->password)) {
            $this->addError('deletePassword', 'Palavra-passe inválida.');
            return;
        }

        $userId = $this->user->id;

        try {
            DB::transaction(function () use ($userId) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->delete();
                }
            });
        } catch (Throwable) {
            session()->flash('error', 'Não foi possível remover a conta automaticamente. Contacte o suporte.');
            return;
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/')->with('success', 'Conta removida com sucesso.');
    }

    public function render()
    {
        return view('livewire.client.settings')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Configurações']);
    }
}
