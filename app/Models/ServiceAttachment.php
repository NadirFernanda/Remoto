<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAttachment extends Model
{
    protected $fillable = [
        'service_id', 'user_id', 'filename', 'path', 'size', 'mime_type',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 0) . ' KB';
        return $bytes . ' B';
    }
}
