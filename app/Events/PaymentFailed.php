<?php

namespace App\Events;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ?Service $service,
        public User $user,
        public float $amount,
        public string $reason,
        public string $paymentMethod = 'card',
        public array $paymentData = []
    ) {}
}