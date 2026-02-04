<?php

namespace App\Listeners;

use App\Events\FreelancerRegistered;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    public function handle(FreelancerRegistered $event)
    {
        $user = $event->user;
        Mail::raw('Bem-vindo ao site freelancer, ' . $user->name . '!', function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Bem-vindo ao Site Freelancer!');
        });
    }
}
