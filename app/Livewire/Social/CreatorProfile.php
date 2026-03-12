<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\SocialPost;
use App\Models\SocialLike;
use App\Models\SocialComment;
use App\Models\SocialReport;
use Illuminate\Support\Facades\Auth;

class CreatorProfile extends Component
{
    use WithPagination;

    public User $creator;

    public string $commentText = '';
    public ?int $commentingPostId = null;
    public ?int $reportingPostId = null;
    public string $reportReason = '';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'commentText' => 'required|string|min:1|max:1000',
        'reportReason' => 'required|string|max:500',
    ];

    public function mount(User $user): void
    {
        $this->creator = $user;
    }

    public function render()
    {
        $posts = SocialPost::with(['user', 'images', 'likes', 'comments.user'])
            ->where('user_id', $this->creator->id)
            ->active()
            ->latest()
            ->paginate(9);

        $followersCount = $this->creator->followersCount();
        $isFollowing = Auth::check()
            ? Auth::user()->following()->where('following_id', $this->creator->id)->exists()
            : false;

        return view('livewire.social.creator-profile', compact('posts', 'followersCount', 'isFollowing'))
            ->layout('layouts.main', [
                'title' => $this->creator->name . ' — Perfil de Criador',
            ]);
    }

    // ── Toggle follow ─────────────────────────────────────────────────────────

    public function toggleFollow(): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->dispatch('need-login');
            return;
        }

        if ($user->id === $this->creator->id) {
            return;
        }

        if ($user->following()->where('following_id', $this->creator->id)->exists()) {
            $user->following()->detach($this->creator->id);
        } else {
            $user->following()->syncWithoutDetaching([$this->creator->id]);
        }
    }

    // ── Toggle like ───────────────────────────────────────────────────────────

    public function toggleLike(int $postId): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->dispatch('need-login');
            return;
        }

        $existing = SocialLike::where('post_id', $postId)->where('user_id', $user->id)->first();
        if ($existing) {
            $existing->delete();
        } else {
            SocialLike::create(['post_id' => $postId, 'user_id' => $user->id]);
        }
    }

    // ── Comments ──────────────────────────────────────────────────────────────

    public function openComments(int $postId): void
    {
        $this->commentingPostId = ($this->commentingPostId === $postId) ? null : $postId;
        $this->commentText = '';
    }

    public function submitComment(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $this->validateOnly('commentText');

        SocialComment::create([
            'post_id' => $this->commentingPostId,
            'user_id' => $user->id,
            'content' => $this->commentText,
        ]);

        $this->commentText = '';
    }

    // ── Delete post (only own posts) ─────────────────────────────────────────

    public function deletePost(int $postId): void
    {
        $user = Auth::user();
        if (!$user) return;

        $post = SocialPost::where('id', $postId)->where('user_id', $user->id)->firstOrFail();
        foreach ($post->images as $image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->path);
        }
        $post->delete();

        session()->flash('success', 'Publicação removida.');
    }

    // ── Report post ───────────────────────────────────────────────────────────

    public function openReportPost(int $postId): void
    {
        $this->reportingPostId = $postId;
        $this->reportReason = '';
    }

    public function cancelReport(): void
    {
        $this->reportingPostId = null;
        $this->reportReason = '';
    }

    public function submitReport(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $this->validateOnly('reportReason');

        $alreadyReported = SocialReport::where('reportable_type', 'post')
            ->where('reportable_id', $this->reportingPostId)
            ->where('reporter_id', $user->id)
            ->exists();

        if (!$alreadyReported) {
            SocialReport::create([
                'reportable_type' => 'post',
                'reportable_id'   => $this->reportingPostId,
                'reporter_id'     => $user->id,
                'reason'          => $this->reportReason,
                'status'          => 'pendente',
            ]);

            $count = SocialReport::where('reportable_type', 'post')
                ->where('reportable_id', $this->reportingPostId)
                ->where('status', 'pendente')
                ->count();
            if ($count >= 3) {
                SocialPost::where('id', $this->reportingPostId)->update(['status' => 'reported']);
            }
        }

        $this->cancelReport();
        session()->flash('success', 'Denúncia enviada com sucesso.');
    }
}
