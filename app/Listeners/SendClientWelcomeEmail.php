<?php

namespace App\Listeners;

use App\Events\ClientRegistered;
use Illuminate\Support\Facades\Mail;

class SendClientWelcomeEmail
{
    public function handle(ClientRegistered $event)
    {
        $user = $event->user;
        Mail::raw('Bem-vindo ao site freelancer, ' . $user->name . '!', function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Bem-vindo ao Site Freelancer!');
        });
    }
}
