<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AffiliateCommissionEarned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $affiliate,
        public User $referred,
        public float $commission,
        public string $reason = 'signup'
    ) {}
}
