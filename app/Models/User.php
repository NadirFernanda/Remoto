<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Models\CreatorSubscription;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    /**
     * Campos seguros para mass assignment.
     * NUNCA adicionar: role, admin_role, kyc_status, is_suspended,
     * email_verified_at, status, *_suspended — atribuir explicitamente via código.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'affiliate_code',
        'phone',
        'bio',
        'profile_photo',
        'cover_photo',
        'location',
        'has_freelancer_profile',
        'has_cliente_profile',
        'has_creator_profile',
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

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'target_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'author_id');
    }

    public function averageRating(): float
    {
        $avg = $this->reviewsReceived()->avg('rating');
        return $avg ? round($avg, 1) : 0;
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

    // ── Social Module ────────────────────────────────────────────────────────

    public function socialPosts()
    {
        return $this->hasMany(SocialPost::class);
    }

    /** Users this user is following */
    public function following()
    {
        return $this->belongsToMany(User::class, 'social_follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    /** Users that follow this user */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'social_follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    public function followersCount(): int
    {
        return $this->followers()->count();
    }

    public function isFollowedBy(int $userId): bool
    {
        return $this->followers()->where('follower_id', $userId)->exists();
    }

    public function socialLikes()
    {
        return $this->hasMany(SocialLike::class);
    }

    public function stories()
    {
        return $this->hasMany(SocialStory::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(SocialBookmark::class);
    }

    public function hasActiveStories(): bool
    {
        return $this->stories()->where('expires_at', '>', now())->exists();
    }

    // ── Creator Module ───────────────────────────────────────────────────────

    public function creatorProfile()
    {
        return $this->hasOne(CreatorProfile::class);
    }

    /** Subscriptions where this user is the creator (fans subscribing to them) */
    public function subscriptionsAsCreator()
    {
        return $this->hasMany(CreatorSubscription::class, 'creator_id');
    }

    /** Subscriptions this user has purchased (creators they follow) */
    public function subscriptionsAsSubscriber()
    {
        return $this->hasMany(CreatorSubscription::class, 'subscriber_id');
    }

    public function isSubscribedTo(int $creatorId): bool
    {
        return $this->subscriptionsAsSubscriber()
            ->where('creator_id', $creatorId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function isCreator(): bool
    {
        return (bool) $this->has_creator_profile;
    }

    /** All profile types this user has activated */
    public function availableProfiles(): array
    {
        $profiles = [];
        if ($this->has_freelancer_profile || $this->role === 'freelancer') {
            $profiles[] = 'freelancer';
        }
        if ($this->has_cliente_profile || $this->role === 'cliente') {
            $profiles[] = 'cliente';
        }
        if ($this->has_creator_profile || $this->role === 'creator') {
            $profiles[] = 'creator';
        }
        return $profiles;
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
        if ($sessionRole && in_array($sessionRole, ['cliente', 'freelancer', 'creator'])) {
            return $sessionRole;
        }
        return $this->role;
    }

    /**
     * Check if the user can switch roles (not admin).
     */
    public function canSwitchRole(): bool
    {
        return !in_array($this->role, ['admin']);
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

    public function coverPhotoUrl(): ?string
    {
        return $this->cover_photo ? Storage::url($this->cover_photo) : null;
    }

    // ── Admin Relations ──────────────────────────────────────────────────────

    public function adminPermissions()
    {
        return $this->hasMany(AdminPermission::class);
    }

    public function adminSecurity()
    {
        return $this->hasOne(AdminSecurity::class);
    }

    public function adminNotifications()
    {
        return $this->hasOne(AdminNotificationPreference::class);
    }

    /**
     * Get the access level for a specific module.
     * Master always gets 'full'. Others read from admin_permissions table.
     */
    public function adminModuleAccess(string $module): string
    {
        if ($this->role !== 'admin') {
            return 'none';
        }
        if ($this->admin_role === 'master' || $this->admin_role === null) {
            return 'full';
        }
        $perm = $this->adminPermissions()->where('module', $module)->first();
        return $perm ? $perm->access : 'none';
    }

    /**
     * Human-readable label for admin_role.
     */
    public function adminRoleLabel(): string
    {
        return match ($this->admin_role) {
            'master'     => 'Admin Master',
            'financeiro' => 'Diretor Financeiro',
            'gestor'     => 'Gestor de Operações',
            'suporte'    => 'Gestor de Suporte',
            'analista'   => 'Analista de Dados',
            default      => 'Administrador',
        };
    }
}
