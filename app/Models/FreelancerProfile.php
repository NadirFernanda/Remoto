<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreelancerProfile extends Model
{
    protected $fillable = [
        'user_id', 'headline', 'summary', 'hourly_rate', 'currency', 'availability_status', 'skills', 'languages', 'metrics', 'kyc_status'
    ];

    protected $casts = [
        'skills' => 'array',
        'languages' => 'array',
        'metrics' => 'array',
        'hourly_rate' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photoUrl()
    {
        if ($this->user && $this->user->profile_photo) {
            return \Illuminate\Support\Facades\Storage::url($this->user->profile_photo);
        }
        return asset('build/img/default-avatar.png');
    }
}
