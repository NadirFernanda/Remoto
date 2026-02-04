<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\FreelancerRegistered;
use App\Listeners\CreateFreelancerProfile;
use App\Listeners\SendWelcomeEmail;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\FreelancerRegistered::class => [
            \App\Listeners\CreateFreelancerProfile::class,
            \App\Listeners\SendWelcomeEmail::class,
        ],
        \App\Events\ClientRegistered::class => [
            \App\Listeners\CreateClientProfile::class,
            \App\Listeners\SendClientWelcomeEmail::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
