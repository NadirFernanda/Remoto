<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SocialPost;
use App\Models\SocialPostImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CreatePost extends Component
{
    use WithFileUploads;

    public string $content = '';
    public array $photos = [];
    public bool $hasSubmitted = false;

    protected $rules = [
        'content'   => 'required|string|min:10|max:2000',
        'photos'    => 'array|max:5',
        'photos.*'  => 'image|max:4096', // 4 MB each
    ];

    protected $messages = [
        'content.required' => 'O conteúdo da publicação é obrigatório.',
        'content.min'      => 'A publicação deve ter pelo menos 10 caracteres.',
        'content.max'      => 'A publicação não pode ter mais de 2000 caracteres.',
        'photos.max'       => 'Pode fazer upload de no máximo 5 imagens.',
        'photos.*.image'   => 'Apenas imagens são permitidas.',
        'photos.*.max'     => 'Cada imagem não pode ultrapassar 4 MB.',
    ];

    public function mount(): void
    {
        if (!Auth::check() || Auth::user()->activeRole() !== 'freelancer') {
            abort(403, 'Apenas freelancers podem publicar conteúdo.');
        }
    }

    public function render()
    {
        return view('livewire.social.create-post')
            ->layout('layouts.dashboard', [
                'dashboardTitle' => 'Nova Publicação',
            ]);
    }

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();

        $post = SocialPost::create([
            'user_id' => $user->id,
            'content' => $this->content,
            'status'  => 'active',
        ]);

        foreach ($this->photos as $index => $photo) {
            $path = $photo->store('social/posts', 'public');
            SocialPostImage::create([
                'post_id' => $post->id,
                'path'    => $path,
                'order'   => $index,
            ]);
        }

        $this->reset('content', 'photos');
        $this->hasSubmitted = true;

        session()->flash('success', 'Publicação criada com sucesso!');
        $this->redirect(route('social.creator', ['user' => $user->id]));
    }

    public function removePhoto(int $index): void
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }
}
