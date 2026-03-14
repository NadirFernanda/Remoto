<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialPost;
use App\Modules\Social\Services\FeedService;
use App\Modules\Social\Services\PostService;
use App\Modules\Social\Services\SocialInteractionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Feed extends Component
{
    use WithPagination;

    public string $commentText = '';
    public ?int $commentingPostId = null;
    public ?int $editingPostId = null;
    public string $editContent = '';
    public ?int $reportingPostId = null;
    public ?int $reportingUserId = null;
    public string $reportReason = '';
    public string $reportType = '';

    // Filters
    public string $hashtag = '';
    public bool $bookmarkedOnly = false;
    public bool $myPostsOnly = false;

    protected $paginationTheme = 'tailwind';

    protected $queryString = ['hashtag', 'bookmarkedOnly', 'myPostsOnly'];

    protected $rules = [
        'commentText'  => 'required|string|min:1|max:1000',
        'reportReason' => 'required|string|max:500',
        'editContent'  => 'required|string|min:1|max:3000',
    ];

    // ── Feed query ────────────────────────────────────────────────────────────

    public function render()
    {
        $user    = Auth::user();
        $service = app(FeedService::class);

        $posts   = $service->getFeed($user, $this->hashtag, $this->bookmarkedOnly, $this->myPostsOnly);
        $isEmpty = $service->isEmptyFeed($user, $this->hashtag, $this->bookmarkedOnly, $this->myPostsOnly);

        try {
            $subscribedCreatorIds = $user
                ? $user->subscriptionsAsSubscriber()->active()->pluck('creator_id')->toArray()
                : [];
        } catch (\Throwable $e) {
            $subscribedCreatorIds = [];
        }

        return view('livewire.social.feed', compact('posts', 'isEmpty', 'subscribedCreatorIds'))
            ->layout('layouts.public', ['title' => $this->hashtag ? '#' . $this->hashtag : 'Feed Social']);
    }

    // ── Toggle like ───────────────────────────────────────────────────────────

    public function toggleLike(int $postId): void
    {
        $user = Auth::user();
        if (!$user) { $this->dispatch('need-login'); return; }

        app(SocialInteractionService::class)->toggleLike($user, $postId);
    }

    // ── Toggle bookmark ───────────────────────────────────────────────────────

    public function toggleBookmark(int $postId): void
    {
        $user = Auth::user();
        if (!$user) { $this->dispatch('need-login'); return; }

        app(SocialInteractionService::class)->toggleBookmark($user, $postId);
    }

    // ── Toggle follow ─────────────────────────────────────────────────────────

    public function toggleFollow(int $creatorId): void
    {
        $user = Auth::user();
        if (!$user) { $this->dispatch('need-login'); return; }

        app(SocialInteractionService::class)->toggleFollow($user, $creatorId);
    }

    // ── Delete post ───────────────────────────────────────────────────────────

    // ── Edit post ─────────────────────────────────────────────────────────────

    public function openEditPost(int $postId): void
    {
        $user = Auth::user();
        $post = SocialPost::where('id', $postId)->where('user_id', $user?->id)->first();
        if (!$post) return;

        $this->editingPostId = $postId;
        $this->editContent   = $post->content;
    }

    public function saveEditPost(): void
    {
        $this->validate(['editContent' => 'required|string|min:1|max:3000']);

        $user = Auth::user();
        $post = SocialPost::where('id', $this->editingPostId)->where('user_id', $user?->id)->first();
        if (!$post) return;

        app(PostService::class)->update($post, $this->editContent);
        $this->reset('editingPostId', 'editContent');
    }

    public function cancelEditPost(): void
    {
        $this->reset('editingPostId', 'editContent');
    }

    public function deletePost(int $postId): void
    {
        $user = Auth::user();
        $post = SocialPost::where('id', $postId)->where('user_id', $user?->id)->first();
        if (!$post) return;

        app(PostService::class)->delete($post);
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

        app(SocialInteractionService::class)->addComment($user, $this->commentingPostId, $this->commentText);
        $this->commentText = '';
    }

    // ── Reports ───────────────────────────────────────────────────────────────

    public function openReportPost(int $postId): void
    {
        $this->reportingPostId = $postId;
        $this->reportingUserId = null;
        $this->reportType      = 'post';
        $this->reportReason    = '';
    }

    public function openReportUser(int $userId): void
    {
        $this->reportingUserId = $userId;
        $this->reportingPostId = null;
        $this->reportType      = 'user';
        $this->reportReason    = '';
    }

    public function cancelReport(): void
    {
        $this->reportingPostId = null;
        $this->reportingUserId = null;
        $this->reportReason    = '';
        $this->reportType      = '';
    }

    public function submitReport(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $this->validateOnly('reportReason');

        $id = $this->reportType === 'post' ? $this->reportingPostId : $this->reportingUserId;
        app(SocialInteractionService::class)->report($user, $this->reportType, $id, $this->reportReason);

        $this->cancelReport();
        session()->flash('success', 'Denúncia enviada. Obrigado por manter a plataforma segura.');
    }
}
