<?php

namespace App\Listeners;

use App\Events\ClientRegistered;
use App\Mail\WelcomeClientMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendClientWelcomeEmail implements ShouldQueue
{
    public string $queue = 'emails';

    public function handle(ClientRegistered $event): void
    {
        Mail::to($event->user->email)->send(new WelcomeClientMail($event->user));
    }
}
