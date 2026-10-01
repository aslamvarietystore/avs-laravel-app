<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\DB;

class ExchangeRateService
{
    public function currentRate(string $from = 'CNY', string $to = 'PKR'): ?ExchangeRate
    {
        return ExchangeRate::query()
            ->where('currency_from', strtoupper($from))
            ->where('currency_to', strtoupper($to))
            ->where('is_active', true)
            ->orderByDesc('effective_date')
            ->orderByDesc('id')
            ->first();
    }

    public function record(
        string $from,
        string $to,
        float $rate,
        string $effectiveDate,
        bool $isActive = true,
        ?string $notes = null,
        ?int $createdBy = null,
    ): ExchangeRate {
        return DB::transaction(function () use ($from, $to, $rate, $effectiveDate, $isActive, $notes, $createdBy): ExchangeRate {
            if ($isActive) {
                $this->deactivate($from, $to);
            }

            return ExchangeRate::query()->create([
                'currency_from' => strtoupper($from),
                'currency_to' => strtoupper($to),
                'rate' => $rate,
                'effective_date' => $effectiveDate,
                'is_active' => $isActive,
                'notes' => $notes,
                'created_by' => $createdBy,
            ]);
        });
    }

    public function activate(ExchangeRate $exchangeRate): ExchangeRate
    {
        return DB::transaction(function () use ($exchangeRate): ExchangeRate {
            $this->deactivate($exchangeRate->currency_from, $exchangeRate->currency_to);
            $exchangeRate->update(['is_active' => true]);

            return $exchangeRate->refresh();
        });
    }

    public function deactivate(string $from, string $to): int
    {
        return ExchangeRate::query()
            ->where('currency_from', strtoupper($from))
            ->where('currency_to', strtoupper($to))
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}
