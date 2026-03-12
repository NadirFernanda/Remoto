<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialReport extends Model
{
    protected $table = 'social_reports';

    protected $fillable = [
        'reportable_type',
        'reportable_id',
        'reporter_id',
        'reason',
        'status',
        'admin_note',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    // Polymorphic-style accessor
    public function getSubjectAttribute()
    {
        if ($this->reportable_type === 'post') {
            return SocialPost::find($this->reportable_id);
        }
        if ($this->reportable_type === 'user') {
            return User::find($this->reportable_id);
        }
        return null;
    }
}
