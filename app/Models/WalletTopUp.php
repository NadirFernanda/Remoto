<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTopUp extends Model
{
    protected $fillable = [
        'user_id',
        'valor',
        'payment_method_used',
        'appypay_charge_id',
        'payment_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
