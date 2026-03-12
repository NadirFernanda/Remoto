<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SocialPost;
use App\Models\SocialPostMedia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CreatePost extends Component
{
    use WithFileUploads;

    // Post type: text | image | video | audio | link | repost
    public string $postType = 'text';
    public string $content = '';
    public string $visibility = 'public';

    // Image uploads (up to 5)
    public array $photos = [];

    // Video upload (single, max 200 MB)
    public $video = null;

    // Audio upload (single, max 50 MB)
    public $audio = null;

    // Link post
    public string $linkUrl = '';
    public string $linkTitle = '';
    public string $linkDescription = '';
    public string $linkImage = '';

    // Repost
    public ?int $repostId = null;
    public ?SocialPost $repostPost = null;

    protected function rules(): array
    {
        $base = [
            'visibility' => 'required|in:public,followers',
            'content'    => 'nullable|string|max:3000',
        ];

        return match ($this->postType) {
            'image' => array_merge($base, [
                'photos'   => 'required|array|min:1|max:5',
                'photos.*' => 'image|max:8192',
            ]),
            'video' => array_merge($base, [
                'video' => 'required|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo|max:204800',
            ]),
            'audio' => array_merge($base, [
                'audio' => 'required|file|mimetypes:audio/mpeg,audio/mp4,audio/ogg,audio/wav,audio/x-wav|max:51200',
            ]),
            'link' => array_merge($base, [
                'linkUrl' => 'required|url|max:2000',
            ]),
            'repost' => array_merge($base, [
                'repostId' => 'required|integer|exists:social_posts,id',
                'content'  => 'nullable|string|max:1000',
            ]),
            default => array_merge($base, [
                'content' => 'required|string|min:5|max:3000',
            ]),
        };
    }

    protected $messages = [
        'content.required'  => 'Escreva algo para publicar.',
        'content.min'       => 'A publicação deve ter pelo menos 5 caracteres.',
        'photos.required'   => 'Selecione pelo menos uma imagem.',
        'photos.max'        => 'Máximo de 5 imagens por publicação.',
        'photos.*.image'    => 'Apenas imagens são permitidas (JPG, PNG, GIF, WebP).',
        'photos.*.max'      => 'Cada imagem não pode ultrapassar 8 MB.',
        'video.required'    => 'Selecione um vídeo para publicar.',
        'video.mimetypes'   => 'Formato não suportado. Use MP4, WebM ou MOV.',
        'video.max'         => 'O vídeo não pode ultrapassar 200 MB.',
        'audio.required'    => 'Selecione um ficheiro de áudio.',
        'audio.mimetypes'   => 'Formato não suportado. Use MP3, M4A, OGG ou WAV.',
        'audio.max'         => 'O áudio não pode ultrapassar 50 MB.',
        'linkUrl.required'  => 'Introduza um URL válido.',
        'linkUrl.url'       => 'O URL não é válido.',
        'repostId.required' => 'Publicação de origem inválida.',
        'repostId.exists'   => 'A publicação selecionada não existe.',
    ];

    public function mount(?int $repost_id = null): void
    {
        if (!Auth::check() || Auth::user()->activeRole() !== 'freelancer') {
            abort(403, 'Apenas freelancers podem publicar conteúdo.');
        }

        if ($repost_id) {
            $this->repostId   = $repost_id;
            $this->repostPost = SocialPost::with(['user', 'media'])->find($repost_id);
            $this->postType   = 'repost';
        }
    }

    public function setType(string $type): void
    {
        $this->postType = $type;
        $this->resetValidation();
        $this->reset('photos', 'video', 'audio', 'linkUrl', 'linkTitle', 'linkDescription', 'linkImage');
    }

    public function removePhoto(int $index): void
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function removeVideo(): void { $this->reset('video'); }
    public function removeAudio(): void { $this->reset('audio'); }

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();

        $post = SocialPost::create([
            'user_id'          => $user->id,
            'content'          => $this->content,
            'type'             => $this->postType,
            'visibility'       => $this->visibility,
            'status'           => 'active',
            'link_url'         => $this->postType === 'link' ? $this->linkUrl : null,
            'link_title'       => $this->postType === 'link' ? ($this->linkTitle ?: null) : null,
            'link_description' => $this->postType === 'link' ? ($this->linkDescription ?: null) : null,
            'link_image'       => $this->postType === 'link' ? ($this->linkImage ?: null) : null,
            'repost_id'        => $this->postType === 'repost' ? $this->repostId : null,
        ]);

        // Store media files
        if ($this->postType === 'image') {
            foreach ($this->photos as $i => $photo) {
                $path = $photo->store('social/images', 'public');
                SocialPostMedia::create([
                    'post_id'       => $post->id,
                    'type'          => 'image',
                    'path'          => $path,
                    'original_name' => $photo->getClientOriginalName(),
                    'mime_type'     => $photo->getMimeType(),
                    'file_size'     => $photo->getSize(),
                    'order'         => $i,
                ]);
            }
        } elseif ($this->postType === 'video') {
            $path = $this->video->store('social/videos', 'public');
            SocialPostMedia::create([
                'post_id'       => $post->id,
                'type'          => 'video',
                'path'          => $path,
                'original_name' => $this->video->getClientOriginalName(),
                'mime_type'     => $this->video->getMimeType(),
                'file_size'     => $this->video->getSize(),
            ]);
        } elseif ($this->postType === 'audio') {
            $path = $this->audio->store('social/audio', 'public');
            SocialPostMedia::create([
                'post_id'       => $post->id,
                'type'          => 'audio',
                'path'          => $path,
                'original_name' => $this->audio->getClientOriginalName(),
                'mime_type'     => $this->audio->getMimeType(),
                'file_size'     => $this->audio->getSize(),
            ]);
        }

        session()->flash('success', 'Publicação criada com sucesso!');
        $this->redirect(route('social.feed'));
    }

    public function render()
    {
        return view('livewire.social.create-post')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Nova Publicação']);
    }
}
