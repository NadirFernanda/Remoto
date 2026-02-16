<?php

if (!function_exists('convert_brl_to_aoa')) {
    /**
     * Converte um valor em BRL para AOA usando a taxa em config('currency.aoa_per_brl')
     * @param float $amount valor em BRL
     * @return float valor em AOA
     */
    function convert_brl_to_aoa($amount, $useLive = false)
    {
        $rate = floatval(config('currency.aoa_per_brl', 41.5));
        if ($useLive) {
            $live = fetch_live_aoa_rate();
            if ($live && is_numeric($live)) {
                $rate = floatval($live);
            }
        }
        return round(floatval($amount) * $rate, (int) config('currency.decimals', 2));
    }
}

if (!function_exists('money_aoa')) {
    /**
     * Formata um valor (assumido em BRL) para exibir em AOA com símbolo.
     * Se $convert for false, apenas formata o valor sem converter.
     * @param float $amount
     * @param bool $convert
     * @return string
     */
    function money_aoa($amount, $convert = true)
    {
        $decimals = (int) config('currency.decimals', 2);
        $thousand = config('currency.thousand_sep', '.');
        $decimal = config('currency.decimal_sep', ',');
        $symbol = config('currency.symbol', 'Kz');

        if ($convert) {
            // prefer cached live rate via ExchangeRateService when available
            try {
                if (class_exists('\App\\Services\\ExchangeRateService')) {
                    $svc = app(\App\Services\ExchangeRateService::class);
                    $rate = $svc->getRate();
                    $value = round(floatval($amount) * $rate, $decimals);
                } else {
                    $value = convert_brl_to_aoa($amount);
                }
            } catch (\Throwable $e) {
                $value = convert_brl_to_aoa($amount);
            }
        } else {
            $value = floatval($amount);
        }
        // ensure numeric
        $value = number_format($value, $decimals, $decimal, $thousand);
        return $symbol . ' ' . $value;
    }
}

if (!function_exists('fetch_live_aoa_rate')) {
    /**
     * Consulta uma API pública para obter a taxa BRL -> AOA mais recente.
     * Não aplica cache — deixe o cache/fallback para a camada chamadora.
     * Retorna float da taxa ou null em caso de erro.
     *
     * @param string $source
     * @return float|null
     */
    function fetch_live_aoa_rate($source = 'exchangerate.host')
    {
        try {
            // Use Laravel HTTP client
            if (!class_exists('\Illuminate\Support\Facades\Http')) {
                return null;
            }
            $url = 'https://api.exchangerate.host/latest?base=BRL&symbols=AOA';
            $resp = \Illuminate\Support\Facades\Http::get($url);
            if ($resp->ok()) {
                $data = $resp->json();
                if (isset($data['rates']['AOA'])) {
                    return floatval($data['rates']['AOA']);
                }
            }
        } catch (\Throwable $e) {
            // swallow — caller will fallback
        }
        return null;
    }
}
