<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialPost;
use App\Models\SocialLike;
use App\Models\SocialComment;
use App\Models\SocialReport;
use Illuminate\Support\Facades\Auth;

class Feed extends Component
{
    use WithPagination;

    public string $commentText = '';
    public ?int $commentingPostId = null;
    public ?int $reportingPostId = null;
    public ?int $reportingUserId = null;
    public string $reportReason = '';
    public string $reportType = '';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'commentText' => 'required|string|min:1|max:1000',
        'reportReason' => 'required|string|max:500',
    ];

    // ── Feed query ────────────────────────────────────────────────────────────

    public function render()
    {
        $user = Auth::user();

        if ($user) {
            $followingIds = $user->following()->pluck('users.id');

            if ($followingIds->isEmpty()) {
                // Show recent posts from all active freelancers for discovery
                $posts = SocialPost::with(['user', 'images', 'likes', 'comments.user'])
                    ->active()
                    ->latest()
                    ->paginate(10);
                $isEmpty = true;
            } else {
                $posts = SocialPost::with(['user', 'images', 'likes', 'comments.user'])
                    ->active()
                    ->whereIn('user_id', $followingIds)
                    ->latest()
                    ->paginate(10);
                $isEmpty = false;
            }
        } else {
            // Guest: show all active posts
            $posts = SocialPost::with(['user', 'images', 'likes', 'comments.user'])
                ->active()
                ->latest()
                ->paginate(10);
            $isEmpty = false;
        }

        return view('livewire.social.feed', compact('posts', 'isEmpty'))
            ->layout('layouts.main', ['title' => 'Feed Social']);
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

    // ── Toggle follow ─────────────────────────────────────────────────────────

    public function toggleFollow(int $creatorId): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->dispatch('need-login');
            return;
        }

        if ($user->id === $creatorId) {
            return;
        }

        if ($user->isFollowedBy($user->id) || $user->following()->where('following_id', $creatorId)->exists()) {
            $user->following()->detach($creatorId);
        } else {
            $user->following()->syncWithoutDetaching([$creatorId]);
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

    // ── Reports ───────────────────────────────────────────────────────────────

    public function openReportPost(int $postId): void
    {
        $this->reportingPostId = $postId;
        $this->reportingUserId = null;
        $this->reportType = 'post';
        $this->reportReason = '';
    }

    public function openReportUser(int $userId): void
    {
        $this->reportingUserId = $userId;
        $this->reportingPostId = null;
        $this->reportType = 'user';
        $this->reportReason = '';
    }

    public function cancelReport(): void
    {
        $this->reportingPostId = null;
        $this->reportingUserId = null;
        $this->reportReason = '';
        $this->reportType = '';
    }

    public function submitReport(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $this->validateOnly('reportReason');

        $id = $this->reportType === 'post' ? $this->reportingPostId : $this->reportingUserId;

        // Prevent duplicate reports from same user
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

            // Flag post as reported if threshold reached (3+ reports)
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
        session()->flash('success', 'Denúncia enviada. Obrigado por nos ajudar a manter a plataforma segura.');
    }
}
