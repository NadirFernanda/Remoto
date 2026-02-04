<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Service;
use App\Policies\ServicePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Service::class => ServicePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
