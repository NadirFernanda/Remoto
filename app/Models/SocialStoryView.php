<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialStoryView extends Model
{
    public $timestamps = false;

    protected $table = 'social_story_views';

    protected $fillable = ['story_id', 'viewer_id', 'viewed_at'];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function story(): BelongsTo
    {
        return $this->belongsTo(SocialStory::class, 'story_id');
    }

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }
}
