<?php

namespace App\Events;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Service $service,
        public User $freelancer,
        public float $amount
    ) {}
}
