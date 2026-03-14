<?php

namespace App\Listeners;

use App\Events\FreelancerRegistered;
use App\Mail\WelcomeFreelancerMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    public string $queue = 'emails';

    public function handle(FreelancerRegistered $event): void
    {
        Mail::to($event->user->email)->send(new WelcomeFreelancerMail($event->user));
    }
}
