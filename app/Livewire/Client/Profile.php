<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\UserSessionTrait;

class Profile extends Component
{
    use UserSessionTrait;
        // Removed extra opening curly brace
    public $user;
    public $interests_input;

    public function mount()
    {
        $this->user = $this->getCurrentUser();
        $profile = $this->user->profile;
        $this->interests_input = $profile && $profile->interests ? implode(', ', $profile->interests) : '';
    }

    public function saveInterests()
    {
        $this->validate([
            'interests_input' => 'nullable|string|max:1000',
        ]);

        $profile = $this->user->profile;
        if (!$profile) {
            $profile = \App\Models\Profile::create(['user_id' => $this->user->id]);
        }
        $tags = array_filter(array_map('trim', explode(',', $this->interests_input)));
        // limit to 10 tags
        $tags = array_slice($tags, 0, 10);
        $profile->interests = $tags;
        $profile->save();

        session()->flash('success', 'Áreas de interesse salvas.');
        $this->mount();
        $this->emit('profileUpdated');
    }

    public function render()
    {
        return view('livewire.client.profile');
    }
}
