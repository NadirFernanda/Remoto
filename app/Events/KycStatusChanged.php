<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KycStatusChanged
{
    use Dispatchable, SerializesModels;

    /** @param string $status  'verified' | 'rejected' */
    public function __construct(
        public User $user,
        public string $status,
        public ?string $adminNote = null
    ) {}
}
