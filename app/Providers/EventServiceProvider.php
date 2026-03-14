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
        // ── Registo ────────────────────────────────────────────────
        \App\Events\FreelancerRegistered::class => [
            \App\Listeners\CreateFreelancerProfile::class,
            \App\Listeners\SendWelcomeEmail::class,
        ],
        \App\Events\ClientRegistered::class => [
            \App\Listeners\CreateClientProfile::class,
            \App\Listeners\SendClientWelcomeEmail::class,
        ],

        // ── Pagamentos ─────────────────────────────────────────────
        \App\Events\PaymentReceived::class => [
            \App\Listeners\LogPaymentReceived::class,
        ],

        // ── Ciclo de vida do serviço ───────────────────────────────
        \App\Events\ServiceCompleted::class => [
            \App\Listeners\UpdateFreelancerMetricsOnCompletion::class,
        ],

        // ── Disputas ───────────────────────────────────────────────
        \App\Events\DisputeOpened::class => [
            \App\Listeners\NotifyAdminOfDispute::class,
        ],

        // ── Avaliações ─────────────────────────────────────────────
        \App\Events\ReviewSubmitted::class => [
            \App\Listeners\UpdateTargetRatingMetrics::class,
        ],

        // ── Afiliados ──────────────────────────────────────────────
        \App\Events\AffiliateCommissionEarned::class => [
            \App\Listeners\CreditAffiliateCommission::class,
        ],

        // ── KYC ────────────────────────────────────────────────────
        \App\Events\KycStatusChanged::class => [
            \App\Listeners\HandleKycStatusChanged::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
