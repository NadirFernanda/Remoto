<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SocialPostMedia extends Model
{
    protected $table = 'social_post_media';

    protected $fillable = [
        'post_id', 'type', 'path', 'thumbnail_path',
        'original_name', 'mime_type', 'file_size', 'duration', 'order',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class, 'post_id');
    }

    public function url(): string
    {
        return Storage::url($this->path);
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null;
    }

    public function formattedSize(): string
    {
        if (!$this->file_size) return '';
        $kb = $this->file_size / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 1) . ' MB';
    }

    public function formattedDuration(): string
    {
        if (!$this->duration) return '';
        $m = intdiv($this->duration, 60);
        $s = $this->duration % 60;
        return sprintf('%d:%02d', $m, $s);
    }
}
