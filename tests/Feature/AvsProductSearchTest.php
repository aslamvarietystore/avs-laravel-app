<?php

use App\Models\Category;
use App\Models\Product;
use App\Services\PricingService;

it('calculates PKR cost from the China price and exchange rate', function () {
    $service = app(PricingService::class);

    expect($service->calculatePkrCost(8.50, 39.00))->toBe(331.50)
        ->and($service->calculatePkrCost(8.50, 39.00))->toBeFloat();
});

it('finds a product by exact barcode and item number', function () {
    $category = Category::factory()->create(['name' => 'Hair Accessories']);

    Product::factory()->create([
        'category_id' => $category->id,
        'item_number' => 'AVS-000125',
        'barcode' => '8901234567890',
        'name' => 'Butterfly Hair Accessories Organizer',
        'china_price' => 8.50,
        'wholesale_price' => 390,
        'stock_quantity' => 125,
        'status' => 'active',
    ]);

    $barcodeResponse = $this->get('/products/search?query=8901234567890');
    $barcodeResponse->assertOk()->assertSee('Butterfly Hair Accessories Organizer')
        ->assertSee('AVS-000125')
        ->assertSee('8901234567890')
        ->assertSee('331.50');

    $itemResponse = $this->get('/products/search?query=AVS-000125');
    $itemResponse->assertOk()->assertSee('Butterfly Hair Accessories Organizer');
});
