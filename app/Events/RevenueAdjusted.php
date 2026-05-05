<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RevenueAdjusted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public float $amount,
        public string $reason
    ) {}
}