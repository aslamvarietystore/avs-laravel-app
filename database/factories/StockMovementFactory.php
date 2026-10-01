<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type' => 'adjustment',
            'quantity' => 10,
            'previous_quantity' => 90,
            'new_quantity' => 100,
            'reference_type' => 'manual',
            'reference_id' => null,
            'notes' => 'Stock adjustment',
            'created_by' => null,
        ];
    }
}
