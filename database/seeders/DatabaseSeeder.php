<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@avs.local'],
            [
                'name' => 'AVS Admin',
                'role' => 'admin',
                'password' => bcrypt('password'),
            ]
        );

        ExchangeRate::query()->firstOrCreate(
            ['currency_from' => 'CNY', 'currency_to' => 'PKR'],
            [
                'rate' => 39.00,
                'effective_date' => now()->toDateString(),
                'is_active' => true,
                'notes' => 'Default AVS exchange rate',
            ]
        );

        $category = Category::query()->firstOrCreate(
            ['slug' => 'hair-accessories'],
            [
                'name' => 'Hair Accessories',
                'description' => 'Beauty and accessory products',
                'status' => 'active',
            ]
        );

        Product::query()->firstOrCreate(
            ['item_number' => 'AVS-000125'],
            [
                'category_id' => $category->id,
                'barcode' => '8901234567890',
                'sku' => 'HA-BFLY-125',
                'name' => 'Butterfly Hair Accessories Organizer',
                'slug' => 'butterfly-hair-accessories-organizer',
                'description' => 'Wholesale organizer for hair accessories.',
                'china_price' => 8.50,
                'exchange_rate' => 39.00,
                'cost_pkr' => 331.50,
                'wholesale_price' => 390,
                'retail_price' => 450,
                'stock_quantity' => 125,
                'low_stock_threshold' => 10,
                'unit' => 'pcs',
                'status' => 'active',
                'is_featured' => true,
            ]
        );
    }
}
