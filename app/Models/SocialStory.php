<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class SocialStory extends Model
{
    protected $table = 'social_stories';

    protected $fillable = [
        'user_id', 'type', 'media_path', 'caption', 'views_count', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(SocialStoryView::class, 'story_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isViewedBy(int $userId): bool
    {
        return $this->views()->where('viewer_id', $userId)->exists();
    }

    public function mediaUrl(): string
    {
        return Storage::url($this->media_path);
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
