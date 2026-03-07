<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\UserSessionTrait;

class Profile extends Component
{
    use UserSessionTrait;
    use WithFileUploads;

    public $user;
    public $interests_input;
    public $profilePhoto;
    public $currentProfilePhoto;

    public function mount()
    {
        $this->user = $this->getCurrentUser();
        $profile = $this->user->profile;
        $this->interests_input = $profile && $profile->interests ? implode(', ', $profile->interests) : '';
        $this->currentProfilePhoto = $this->user->profile_photo;
    }

    public function updatedProfilePhoto()
    {
        $this->validate([
            'profilePhoto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
        ], [
            'profilePhoto.max'   => 'A imagem deve ter no máximo 8 MB.',
            'profilePhoto.mimes' => 'Formato inválido. Use jpg, png ou webp.',
            'profilePhoto.image' => 'O ficheiro deve ser uma imagem.',
        ]);

        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $this->profilePhoto->store('avatars', 'public');
        $user->profile_photo = $path;
        $user->save();

        $this->currentProfilePhoto = $path;
        $this->profilePhoto = null;
        $this->user = $user;

        session()->flash('success', 'Foto de perfil atualizada com sucesso!');
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
        $tags = array_slice($tags, 0, 10);
        $profile->interests = $tags;
        $profile->save();

        session()->flash('success', 'Áreas de interesse salvas.');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.client.profile');
    }
}
