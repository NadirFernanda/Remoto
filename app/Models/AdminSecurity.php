<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminSecurity extends Model
{
    protected $table = 'admin_security';

    protected $fillable = [
        'user_id',
        'two_factor_required',
        'ip_restriction',
        'allowed_ips',
        'session_timeout_enabled',
        'session_timeout_minutes',
        'force_password_change',
        'password_changed_at',
    ];

    protected $casts = [
        'two_factor_required'      => 'boolean',
        'ip_restriction'           => 'boolean',
        'session_timeout_enabled'  => 'boolean',
        'force_password_change'    => 'boolean',
        'password_changed_at'      => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function allowedIpsArray(): array
    {
        if (empty($this->allowed_ips)) {
            return [];
        }
        return json_decode($this->allowed_ips, true) ?? [];
    }
}
