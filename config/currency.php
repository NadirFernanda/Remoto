<?php

return [
    // Quantas unidades de Kz equivalem a 1 BRL. Pode ser sobrescrito em .env
    'aoa_per_brl' => env('AOA_PER_BRL', 41.5),
    'symbol' => 'Kz',
    'decimals' => 2,
    'thousand_sep' => '.',
    'decimal_sep' => ',',
    // Cache TTL for live rate (seconds)
    'ttl_seconds' => env('AOA_RATE_TTL', 3600),
    // Remote provider identifier (for future use)
    'provider' => env('AOA_RATE_PROVIDER', 'exchangerate.host'),
];
