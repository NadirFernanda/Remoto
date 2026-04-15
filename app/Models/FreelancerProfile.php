<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreelancerProfile extends Model
{
    protected $fillable = [
        'user_id', 'headline', 'summary', 'hourly_rate', 'currency',
        'availability_status', 'skills', 'languages', 'metrics',
        'onboarding_dismissed',
        // kyc_status excluído do fillable — atribuir explicitamente via código (OWASP A03)
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

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class, 'user_id', 'user_id')->orderByDesc('ano_inicio');
    }

    public function educations()
    {
        return $this->hasMany(Education::class, 'user_id', 'user_id')->orderByDesc('ano_inicio');
    }

    public function photoUrl()
    {
        if ($this->user && $this->user->profile_photo) {
            return \Illuminate\Support\Facades\Storage::url($this->user->profile_photo);
        }
        return asset('img/default-avatar.svg');
    }
}
