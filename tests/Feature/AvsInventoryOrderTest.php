<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\OrderService;

it('adjusts stock and records the stock movement', function () {
    $category = Category::factory()->create(['name' => 'Hair Accessories']);

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'item_number' => 'AVS-000200',
        'barcode' => '8901234567891',
        'name' => 'Hair Clip',
        'china_price' => 2.50,
        'exchange_rate' => 39.00,
        'wholesale_price' => 150,
        'stock_quantity' => 100,
    ]);

    $inventory = app(InventoryService::class);
    $inventory->adjustStock($product, 25, 'adjustment', 'Store adjustment');

    expect($product->fresh()->stock_quantity)->toBe(125)
        ->and($product->stockMovements()->count())->toBe(1);
});

it('creates an order and reduces product stock', function () {
    $category = Category::factory()->create(['name' => 'Toys']);

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'item_number' => 'AVS-000201',
        'barcode' => '8901234567892',
        'name' => 'Toy Car',
        'china_price' => 12.00,
        'exchange_rate' => 39.00,
        'wholesale_price' => 480,
        'stock_quantity' => 20,
    ]);

    $customer = Customer::factory()->create([
        'name' => 'Retail Test',
        'phone' => '03001234567',
    ]);

    $order = app(OrderService::class)->createOrder(
        $customer,
        [[
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 480,
        ]],
        [
            'notes' => 'Wholesale order',
        ]
    );

    expect((float) $order->total)->toBe(960.0)
        ->and($product->fresh()->stock_quantity)->toBe(18)
        ->and($order->items()->count())->toBe(1);
});
