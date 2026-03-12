<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SocialStory;
use App\Models\SocialStoryView;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Stories extends Component
{
    use WithFileUploads;

    public bool $createModal = false;
    public $storyFile = null;
    public string $storyCaption = '';

    protected $rules = [
        'storyFile'    => 'required|file|max:51200',
        'storyCaption' => 'nullable|string|max:300',
    ];

    protected $messages = [
        'storyFile.required' => 'Selecione uma imagem ou vídeo para o story.',
        'storyFile.max'      => 'O ficheiro não pode ultrapassar 50 MB.',
        'storyCaption.max'   => 'A legenda não pode ter mais de 300 caracteres.',
    ];

    public function publishStory(): void
    {
        $user = Auth::user();
        if (!$user || $user->activeRole() !== 'freelancer') return;

        $this->validate();

        $mime    = $this->storyFile->getMimeType();
        $isVideo = str_starts_with($mime, 'video/');
        $path    = $this->storyFile->store('social/stories', 'public');

        SocialStory::create([
            'user_id'    => $user->id,
            'type'       => $isVideo ? 'video' : 'image',
            'media_path' => $path,
            'caption'    => $this->storyCaption ?: null,
            'expires_at' => now()->addHours(24),
        ]);

        $this->reset('storyFile', 'storyCaption');
        $this->createModal = false;
        $this->dispatch('story-created');
    }

    public function markViewed(int $storyId): void
    {
        $user = Auth::user();
        if (!$user) return;

        $story = SocialStory::find($storyId);
        if (!$story || $story->isExpired()) return;

        $created = SocialStoryView::firstOrCreate(
            ['story_id' => $storyId, 'viewer_id' => $user->id],
            ['viewed_at' => now()]
        );

        if ($created->wasRecentlyCreated) {
            $story->increment('views_count');
        }
    }

    public function render()
    {
        $user = Auth::user();

        $storyGroups = collect();

        if ($user) {
            $followingIds = $user->following()->pluck('users.id')->toArray();
            $viewerIds    = array_merge([$user->id], $followingIds);

            $storyGroups = User::whereIn('id', $viewerIds)
                ->whereHas('stories', fn ($q) => $q->active())
                ->with(['stories' => fn ($q) => $q->active()->orderBy('created_at')])
                ->get()
                ->sortByDesc(fn ($u) => $u->id === $user->id ? PHP_INT_MAX : 0)
                ->values()
                ->map(function ($u) use ($user) {
                    $stories = $u->stories->map(fn ($s) => [
                        'id'         => $s->id,
                        'type'       => $s->type,
                        'url'        => $s->mediaUrl(),
                        'caption'    => $s->caption,
                        'viewed'     => $user ? $s->isViewedBy($user->id) : false,
                        'views'      => $s->views_count,
                        'expires_at' => $s->expires_at->toISOString(),
                    ])->values()->toArray();

                    return [
                        'user_id'    => $u->id,
                        'name'       => $u->name,
                        'avatar'     => $u->avatarUrl(),
                        'is_self'    => $user && $u->id === $user->id,
                        'all_viewed' => collect($stories)->every(fn ($s) => $s['viewed']),
                        'stories'    => $stories,
                    ];
                });
        }

        return view('livewire.social.stories', compact('storyGroups'));
    }
}
