<?php

namespace Database\Factories;

use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExchangeRate>
 */
class ExchangeRateFactory extends Factory
{
    protected $model = ExchangeRate::class;

    public function definition(): array
    {
        return [
            'currency_from' => 'CNY',
            'currency_to' => 'PKR',
            'rate' => 39.00,
            'effective_date' => now()->toDateString(),
            'is_active' => true,
            'notes' => 'Default CNY to PKR rate.',
            'created_by' => null,
        ];
    }
}
