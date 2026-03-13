<?php

return [
    App\Providers\AppServiceProvider::class,

    // ─── Module Service Providers ─────────────────────────────────────────────
    App\Modules\Marketplace\MarketplaceServiceProvider::class,
    App\Modules\Social\SocialServiceProvider::class,
    App\Modules\Messaging\MessagingServiceProvider::class,
    App\Modules\Payments\PaymentsServiceProvider::class,
    App\Modules\Admin\AdminServiceProvider::class,
    App\Modules\Loja\LojaServiceProvider::class,
    App\Modules\Wallet\WalletServiceProvider::class,
];
