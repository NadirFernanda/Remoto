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
        'user_id',
        'content',
        'status',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isLikedBy(int $userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function likesCount(): int
    {
        return $this->likes()->count();
    }

    public function commentsCount(): int
    {
        return $this->comments()->count();
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
