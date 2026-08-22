<?php

namespace App\Events;

use App\Models\KycSubmission;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KycSubmissionCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public KycSubmission $submission,
        public User $user,
        public bool $isResubmission
    ) {}
}
