<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\SocialPost;
use App\Models\SocialLike;
use App\Models\SocialComment;
use App\Models\SocialReport;
use App\Models\SocialBookmark;
use App\Models\CreatorSubscription;
use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $user  = Auth::user();
        $posts = SocialPost::with(['user.freelancerProfile', 'media', 'likes', 'comments.user', 'repost.user', 'repost.media'])
            ->where('user_id', $this->creator->id)
            ->active()
            ->latest()
            ->paginate(9);

        $followersCount = $this->creator->followersCount();
        $isFollowing = $user
            ? $user->following()->where('following_id', $this->creator->id)->exists()
            : false;

        $isSubscribed = false;
        $subscribedCreatorIds = [];
        if ($user) {
            try {
                $isSubscribed = CreatorSubscription::where('subscriber_id', $user->id)
                    ->where('creator_id', $this->creator->id)
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->exists();
                $subscribedCreatorIds = CreatorSubscription::where('subscriber_id', $user->id)
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->pluck('creator_id')
                    ->toArray();
            } catch (\Throwable $e) {
                // table may not exist yet
            }
        }

        $subscriptionPrice = $this->creator->creatorProfile?->subscription_price ?? 3000;

        return view('livewire.social.creator-profile', compact(
            'posts', 'followersCount', 'isFollowing', 'isSubscribed', 'subscribedCreatorIds', 'subscriptionPrice'
        ))
            ->layout('layouts.public', [
                'title' => $this->creator->name . ' — Perfil de Criador',
            ]);
    }
    // ── Subscribe (pay via wallet) ─────────────────────────────────────────

    public function subscribe(): void
    {
        $user = Auth::user();
        if (!$user) { $this->dispatch('need-login'); return; }
        if ($user->id === $this->creator->id) return;

        // Already subscribed?
        $existing = CreatorSubscription::where('subscriber_id', $user->id)
            ->where('creator_id', $this->creator->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();
        if ($existing) {
            session()->flash('success', 'Já é assinante deste criador.');
            return;
        }

        $price       = $this->creator->creatorProfile?->subscription_price ?? 3000;
        $platformFee = round($price * 0.15, 2); // 15% platform fee
        $netAmount   = $price - $platformFee;

        // Check subscriber wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 5000, 'taxa_saque' => 0]
        );

        if ($wallet->saldo < $price) {
            session()->flash('error', 'Saldo insuficiente. Recarregue a sua carteira antes de assinar.');
            return;
        }

        DB::transaction(function () use ($user, $price, $platformFee, $netAmount, $wallet) {
            // Deduct from subscriber
            $wallet->decrement('saldo', $price);

            // Credit creator (net amount)
            $creatorWallet = Wallet::firstOrCreate(
                ['user_id' => $this->creator->id],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 5000, 'taxa_saque' => 0]
            );
            $creatorWallet->increment('saldo_pendente', $netAmount);

            // Create subscription
            CreatorSubscription::create([
                'subscriber_id' => $user->id,
                'creator_id'    => $this->creator->id,
                'amount'        => $price,
                'platform_fee'  => $platformFee,
                'net_amount'    => $netAmount,
                'status'        => 'active',
                'starts_at'     => now(),
                'expires_at'    => now()->addMonth(),
            ]);
        });

        session()->flash('success', 'Assinatura activada! Agora tem acesso ao conteúdo exclusivo de ' . $this->creator->name . '.');
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

        $post = SocialPost::where('id', $postId)->where('user_id', $user->id)->first();
        if (!$post) return;

        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->path);
        }
        foreach ($post->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $post->delete();
        session()->flash('success', 'Publicação removida.');
    }

    public function toggleBookmark(int $postId): void
    {
        $user = Auth::user();
        if (!$user) { $this->dispatch('need-login'); return; }

        $existing = SocialBookmark::where('post_id', $postId)->where('user_id', $user->id)->first();
        $existing ? $existing->delete() : SocialBookmark::create(['post_id' => $postId, 'user_id' => $user->id]);
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
