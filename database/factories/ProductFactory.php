<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = 'Butterfly Hair Accessories Organizer';

        return [
            'category_id' => Category::factory(),
            'subcategory_id' => null,
            'brand_id' => null,
            'item_number' => 'AVS-'.$this->faker->unique()->numerify('#####'),
            'barcode' => $this->faker->unique()->ean13(),
            'sku' => 'HA-BFLY-'.$this->faker->unique()->numerify('###'),
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'description' => $this->faker->paragraph(),
            'china_price' => 8.50,
            'exchange_rate' => 39.00,
            'cost_pkr' => 331.50,
            'wholesale_price' => 390,
            'retail_price' => 450,
            'minimum_order_quantity' => 1,
            'stock_quantity' => 125,
            'low_stock_threshold' => 10,
            'unit' => 'pcs',
            'status' => 'active',
            'is_featured' => false,
            'is_new_arrival' => false,
            'is_sale' => false,
        ];
    }

    /**
     * Place the product in a sub-category and keep the parent category in sync.
     */
    public function forSubcategory(Category $subcategory): static
    {
        return $this->state(fn (): array => [
            'category_id' => $subcategory->parent_id ?? $subcategory->id,
            'subcategory_id' => $subcategory->id,
        ]);
    }
}
