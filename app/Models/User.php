<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
        'affiliate_code',
        'status',
        'phone',
        'bio',
        'profile_photo',
        'location',
    ];
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function freelancerProfile()
    {
        return $this->hasOne(FreelancerProfile::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function servicesAsClient()
    {
        return $this->hasMany(Service::class, 'cliente_id');
    }

    public function servicesAsFreelancer()
    {
        return $this->hasMany(Service::class, 'freelancer_id');
    }

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the active role (session-based switch or DB role).
     * Allows toggling between 'cliente' and 'freelancer' without changing DB.
     */
    public function activeRole(): string
    {
        $sessionRole = session('active_role');
        if ($sessionRole && in_array($sessionRole, ['cliente', 'freelancer'])) {
            return $sessionRole;
        }
        return $this->role;
    }

    /**
     * Check if the user can switch roles (not admin).
     */
    public function canSwitchRole(): bool
    {
        return in_array($this->role, ['cliente', 'freelancer']);
    }

    /**
     * Get the opposite role for switching.
     */
    public function switchableRole(): string
    {
        return $this->activeRole() === 'freelancer' ? 'cliente' : 'freelancer';
    }

    public function avatarUrl()
    {
        if ($this->profile_photo) {
            return Storage::url($this->profile_photo);
        }
        // legacy support: Profile.avatar
        if ($this->profile && isset($this->profile->avatar) && $this->profile->avatar) {
            return asset('storage/' . $this->profile->avatar);
        }
        return asset('img/default-avatar.svg');
    }
}
