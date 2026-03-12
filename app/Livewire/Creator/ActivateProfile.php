<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use App\Models\CreatorProfile;
use Illuminate\Support\Facades\Auth;

class ActivateProfile extends Component
{
    public string $targetProfile = 'creator';
    public string $category      = 'geral';
    public string $bio           = '';

    public function mount(string $profile = 'creator'): void
    {
        $this->targetProfile = $profile;
    }

    protected function rules(): array
    {
        if ($this->targetProfile === 'creator') {
            return [
                'category' => 'required|string',
                'bio'      => 'nullable|string|max:600',
            ];
        }
        return [];
    }

    public function activate(): void
    {
        $user = Auth::user();
        $this->validate();

        if ($this->targetProfile === 'creator') {
            CreatorProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['category' => $this->category, 'bio' => $this->bio]
            );
            $user->update(['has_creator_profile' => true]);
            session()->flash('success', 'Perfil de Criador/Seguidor ativado com sucesso!');
            $this->redirect(route('creator.dashboard'), navigate: true);
            return;
        }

        if ($this->targetProfile === 'freelancer') {
            $user->update(['has_freelancer_profile' => true]);
            session()->flash('success', 'Perfil de Freelancer ativado com sucesso!');
            $this->redirect(route('freelancer.dashboard'), navigate: true);
            return;
        }

        if ($this->targetProfile === 'cliente') {
            $user->update(['has_cliente_profile' => true]);
            session()->flash('success', 'Perfil de Cliente ativado com sucesso!');
            $this->redirect(route('client.dashboard'), navigate: true);
            return;
        }
    }

    public function render()
    {
        return view('livewire.creator.activate-profile', [
            'categories' => CreatorProfile::categories(),
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Ativar Perfil']);
    }
}
