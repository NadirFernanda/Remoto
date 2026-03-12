<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SocialPostImage extends Model
{
    protected $table = 'social_post_images';

    protected $fillable = ['post_id', 'path', 'order'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class, 'post_id');
    }

    public function url(): string
    {
        return Storage::url($this->path);
    }
}
