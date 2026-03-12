<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class SocialPost extends Model
{
    protected $table = 'social_posts';

    protected $fillable = [
        'user_id', 'content', 'type',
        'link_url', 'link_title', 'link_description', 'link_image',
        'repost_id', 'visibility', 'views_count', 'status',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Unified media: images, videos, audio, documents */
    public function media(): HasMany
    {
        return $this->hasMany(SocialPostMedia::class, 'post_id')->orderBy('order');
    }

    /** Legacy images (backward compat with old posts) */
    public function images(): HasMany
    {
        return $this->hasMany(SocialPostImage::class, 'post_id')->orderBy('order');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(SocialLike::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(SocialComment::class, 'post_id')->orderBy('created_at');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(SocialReport::class, 'reportable_id')
            ->where('reportable_type', 'post');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(SocialBookmark::class, 'post_id');
    }

    /** The original post being reshared */
    public function repost(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class, 'repost_id');
    }

    /** Posts that repost this post */
    public function reposts(): HasMany
    {
        return $this->hasMany(SocialPost::class, 'repost_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isLikedBy(int $userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isBookmarkedBy(int $userId): bool
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    public function likesCount(): int
    {
        return $this->likes()->count();
    }

    public function commentsCount(): int
    {
        return $this->comments()->count();
    }

    public function repostsCount(): int
    {
        return $this->reposts()->count();
    }

    /**
     * Return content with clickable #hashtags (safe escaped HTML).
     */
    public function contentWithHashtags(): string
    {
        $safe = e($this->content ?? '');
        return (string) preg_replace(
            '/#([a-zA-Z0-9À-ÿ_]+)/u',
            '<a href="' . url('/social') . '?hashtag=$1" class="text-[#00baff] hover:underline">#$1</a>',
            $safe
        );
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->whereIn('status', ['active', 'reported']);
    }
}
