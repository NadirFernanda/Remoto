<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CreatorSubscription extends Model
{
    protected $fillable = [
        'subscriber_id',
        'creator_id',
        'amount',
        'platform_fee',
        'net_amount',
        'status',
        'starts_at',
        'expires_at',
        'cancelled_at',
    ];

    protected $casts = [
        'starts_at'    => 'datetime',
        'expires_at'   => 'datetime',
        'cancelled_at' => 'datetime',
        'amount'       => 'float',
        'platform_fee' => 'float',
        'net_amount'   => 'float',
    ];

    public function subscriber()
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('expires_at', '>', now());
    }
}
