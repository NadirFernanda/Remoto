<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Contract;
use App\Models\Dispute;
use App\Models\Refund;
use App\Models\Review;
use App\Models\Service;
use App\Models\SocialPost;
use App\Models\User;
use App\Models\Wallet;

use App\Policies\ContractPolicy;
use App\Policies\DisputePolicy;
use App\Policies\RefundPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\ServicePolicy;
use App\Policies\SocialPostPolicy;
use App\Policies\UserPolicy;
use App\Policies\WalletPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Service::class    => ServicePolicy::class,
        User::class       => UserPolicy::class,
        Dispute::class    => DisputePolicy::class,
        Refund::class     => RefundPolicy::class,
        Review::class     => ReviewPolicy::class,
        SocialPost::class => SocialPostPolicy::class,
        Contract::class   => ContractPolicy::class,
        Wallet::class     => WalletPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
