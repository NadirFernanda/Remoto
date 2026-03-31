<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotificationPreference extends Model
{
    protected $table = 'admin_notifications';

    protected $fillable = [
        'user_id',
        'notify_new_user',
        'notify_new_dispute',
        'notify_kyc_pending',
        'notify_payout_request',
        'notify_high_value_transaction',
        'notify_system_error',
        'notify_daily_report',
        'channel',
    ];

    protected $casts = [
        'notify_new_user'               => 'boolean',
        'notify_new_dispute'            => 'boolean',
        'notify_kyc_pending'            => 'boolean',
        'notify_payout_request'         => 'boolean',
        'notify_high_value_transaction' => 'boolean',
        'notify_system_error'           => 'boolean',
        'notify_daily_report'           => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
