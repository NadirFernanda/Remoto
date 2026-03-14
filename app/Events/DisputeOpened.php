<?php

namespace App\Events;

use App\Models\Dispute;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DisputeOpened
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Dispute $dispute,
        public Service $service,
        public User $openedBy
    ) {}
}
