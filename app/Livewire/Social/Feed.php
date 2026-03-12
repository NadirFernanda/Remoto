<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialPost;
use App\Models\SocialLike;
use App\Models\SocialComment;
use App\Models\SocialReport;
use App\Models\SocialBookmark;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Feed extends Component
{
    use WithPagination;

    public string $commentText = '';
    public ?int $commentingPostId = null;
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
    ];

    // ── Feed query ────────────────────────────────────────────────────────────

    public function render()
    {
        $user = Auth::user();

        $query = SocialPost::with([
            'user.freelancerProfile',
            'media',
            'likes',
            'comments.user',
            'repost.user',
            'repost.media',
        ])->active();

        if ($this->hashtag) {
            $tag = ltrim($this->hashtag, '#');
            $query->where('content', 'like', '%#' . $tag . '%');
        } elseif ($this->myPostsOnly && $user) {
            $query->where('user_id', $user->id);
        } elseif ($this->bookmarkedOnly && $user) {
            $bookmarkedIds = $user->bookmarks()->pluck('post_id');
            $query->whereIn('id', $bookmarkedIds);
        } elseif ($user) {
            $followingIds = $user->following()->pluck('users.id');
            $hasFollowing = $followingIds->isNotEmpty();
            $visibleIds = $followingIds->push($user->id)->unique()->values();
            $isEmpty = !$hasFollowing;
            $query->whereIn('user_id', $visibleIds);
        }

        $isEmpty = $isEmpty ?? false;
        $posts = $query->latest()->paginate(10);

        try {
            $subscribedCreatorIds = $user
                ? $user->subscriptionsAsSubscriber()->active()->pluck('creator_id')->toArray()
                : [];
        } catch (\Throwable $e) {
            $subscribedCreatorIds = [];
        }

        return view('livewire.social.feed', compact('posts', 'isEmpty', 'subscribedCreatorIds'))
            ->layout('layouts.main', ['title' => $this->hashtag ? '#' . $this->hashtag : 'Feed Social']);
    }

    // ── Toggle like ───────────────────────────────────────────────────────────

    public function toggleLike(int $postId): void
    {
        $user = Auth::user();
        if (!$user) { $this->dispatch('need-login'); return; }

        $existing = SocialLike::where('post_id', $postId)->where('user_id', $user->id)->first();
        $existing ? $existing->delete() : SocialLike::create(['post_id' => $postId, 'user_id' => $user->id]);
    }

    // ── Toggle bookmark ───────────────────────────────────────────────────────

    public function toggleBookmark(int $postId): void
    {
        $user = Auth::user();
        if (!$user) { $this->dispatch('need-login'); return; }

        $existing = SocialBookmark::where('post_id', $postId)->where('user_id', $user->id)->first();
        $existing ? $existing->delete() : SocialBookmark::create(['post_id' => $postId, 'user_id' => $user->id]);
    }

    // ── Toggle follow ─────────────────────────────────────────────────────────

    public function toggleFollow(int $creatorId): void
    {
        $user = Auth::user();
        if (!$user) { $this->dispatch('need-login'); return; }
        if ($user->id === $creatorId) return;

        if ($user->following()->where('following_id', $creatorId)->exists()) {
            $user->following()->detach($creatorId);
        } else {
            $user->following()->syncWithoutDetaching([$creatorId]);
        }
    }

    // ── Delete post ───────────────────────────────────────────────────────────

    public function deletePost(int $postId): void
    {
        $user = Auth::user();
        $post = SocialPost::where('id', $postId)->where('user_id', $user?->id)->first();
        if (!$post) return;

        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->path);
        }
        foreach ($post->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $post->delete();
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

        $alreadyReported = SocialReport::where('reportable_type', $this->reportType)
            ->where('reportable_id', $id)
            ->where('reporter_id', $user->id)
            ->exists();

        if (!$alreadyReported) {
            SocialReport::create([
                'reportable_type' => $this->reportType,
                'reportable_id'   => $id,
                'reporter_id'     => $user->id,
                'reason'          => $this->reportReason,
                'status'          => 'pendente',
            ]);

            if ($this->reportType === 'post') {
                $count = SocialReport::where('reportable_type', 'post')
                    ->where('reportable_id', $id)
                    ->where('status', 'pendente')
                    ->count();
                if ($count >= 3) {
                    SocialPost::where('id', $id)->update(['status' => 'reported']);
                }
            }
        }

        $this->cancelReport();
        session()->flash('success', 'Denúncia enviada. Obrigado por manter a plataforma segura.');
    }
}
