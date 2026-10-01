<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Supplier::factory(),
            'amount' => $this->faker->randomFloat(2, 1000, 50000),
            'method' => $this->faker->randomElement(['cash', 'bank', 'easypaisa', 'jazzcash']),
            'paid_on' => $this->faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'status' => 'paid',
            'reference' => 'PAY-'.$this->faker->unique()->numerify('######'),
            'notes' => $this->faker->sentence(),
        ];
    }
}
