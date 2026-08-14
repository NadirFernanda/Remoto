<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreatorSubscriptionCheckout extends Model
{
    protected $fillable = [
        'subscriber_id',
        'creator_id',
        'amount',
        'payment_method_used',
        'appypay_charge_id',
        'payment_reference',
        'payment_entity',
        'payment_status',
        'subscription_id',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function subscriber()
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function subscription()
    {
        return $this->belongsTo(CreatorSubscription::class, 'subscription_id');
    }
}
