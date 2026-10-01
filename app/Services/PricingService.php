<?php

namespace App\Services;

class PricingService
{
    public function __construct(protected ExchangeRateService $exchangeRateService) {}

    public function getCurrentExchangeRate(string $from = 'CNY', string $to = 'PKR'): float
    {
        $rate = $this->exchangeRateService->currentRate($from, $to)?->rate;

        return (float) ($rate ?? 39.00);
    }

    public function calculatePkrCost(float|int|string $chinaPrice, float|int|string|null $exchangeRate = null): float
    {
        $rate = $exchangeRate === null ? $this->getCurrentExchangeRate() : (float) $exchangeRate;

        return round((float) $chinaPrice * $rate, 2);
    }
}
