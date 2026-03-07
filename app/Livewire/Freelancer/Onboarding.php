<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;

class Onboarding extends Component
{
    public bool $dismissed = false;

    public function dismiss(): void
    {
        $profile = auth()->user()->freelancerProfile;
        if ($profile) {
            $profile->update(['onboarding_dismissed' => true]);
        }
        $this->dismissed = true;
    }

    public function steps(): array
    {
        $user    = auth()->user();
        $profile = $user->freelancerProfile;

        return [
            [
                'key'   => 'perfil',
                'icon'  => 'user',
                'label' => 'Completar perfil',
                'descr' => 'Adicione headline e resumo profissional',
                'done'  => $profile && $profile->headline && $profile->summary,
                'link'  => route('freelancer.profile.edit'),
            ],
            [
                'key'   => 'portfolio',
                'icon'  => 'image',
                'label' => 'Adicionar portfólio',
                'descr' => 'Mostre seus trabalhos anteriores',
                'done'  => $user->portfolios()->count() > 0,
                'link'  => route('freelancer.portfolio'),
            ],
            [
                'key'   => 'kyc',
                'icon'  => 'shield',
                'label' => 'Verificar identidade (KYC)',
                'descr' => 'Aumente sua credibilidade e limite de saques',
                'done'  => $profile && $profile->kyc_status === 'verified',
                'link'  => route('freelancer.settings'),
            ],

            [
                'key'   => 'financeiro',
                'icon'  => 'wallet',
                'label' => 'Conferir painel financeiro',
                'descr' => 'Acompanhe seus ganhos e configure seus dados bancários',
                'done'  => false,
                'link'  => route('freelancer.financial'),
            ],
        ];
    }

    public function render()
    {
        $user    = auth()->user();
        $profile = $user->freelancerProfile;

        if ($this->dismissed || ($profile && $profile->onboarding_dismissed)) {
            return '';
        }

        $steps     = $this->steps();
        $completed = collect($steps)->where('done', true)->count();
        $total     = count($steps);

        if ($completed === $total) {
            return '';
        }

        return view('livewire.freelancer.onboarding', compact('steps', 'completed', 'total'));
    }
}
