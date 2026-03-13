<?php

namespace App\Modules\Admin\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    protected $cacheKey = 'aoa_rate';
    protected $ttlSeconds = 3600; // 1 hour

    public function __construct()
    {
        $this->ttlSeconds = config('currency.ttl_seconds', 3600);
    }

    /**
     * Get rate from cache or fetch live
     * @param bool $forceRefresh
     * @return float
     */
    public function getRate($forceRefresh = false)
    {
        if ($forceRefresh) {
            return $this->refresh();
        }

        return Cache::remember($this->cacheKey, $this->ttlSeconds, function () {
            $live = $this->fetchLiveRate();
            if ($live && is_numeric($live)) {
                return floatval($live);
            }
            return floatval(config('currency.aoa_per_brl', 41.5));
        });
    }

    /**
     * Force refresh the cached rate
     * @return float
     */
    public function refresh()
    {
        $live = $this->fetchLiveRate();
        $rate = ($live && is_numeric($live)) ? floatval($live) : floatval(config('currency.aoa_per_brl', 41.5));
        Cache::put($this->cacheKey, $rate, $this->ttlSeconds);
        return $rate;
    }

    protected function fetchLiveRate()
    {
        try {
            $provider = config('currency.provider', 'exchangerate.host');
            $url = 'https://api.exchangerate.host/latest?base=BRL&symbols=AOA';
            $resp = Http::timeout(6)->get($url);
            if ($resp->ok()) {
                $data = $resp->json();
                if (isset($data['rates']['AOA'])) {
                    return $data['rates']['AOA'];
                }
                \Illuminate\Support\Facades\Log::warning('ExchangeRateService: missing AOA rate in response', ['provider'=>$provider, 'response'=>$data]);
            } else {
                \Illuminate\Support\Facades\Log::warning('ExchangeRateService: non-ok response', ['provider'=>$provider, 'status'=>$resp->status()]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ExchangeRateService: exception fetching rate', ['message'=>$e->getMessage()]);
        }
        return null;
    }
}

