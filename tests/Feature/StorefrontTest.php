<?php

use App\Models\Category;
use App\Models\Product;

it('shows the public storefront and product listing', function () {
    $category = Category::factory()->create(['name' => 'Hair Accessories']);

    Product::factory()->create([
        'category_id' => $category->id,
        'item_number' => 'AVS-000300',
        'barcode' => '8901234567899',
        'name' => 'Butterfly Hair Accessories Organizer',
        'slug' => 'butterfly-hair-accessories-organizer',
        'china_price' => 8.50,
        'exchange_rate' => 39.00,
        'wholesale_price' => 390,
        'retail_price' => 450,
        'stock_quantity' => 125,
        'status' => 'active',
    ]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Aslam Variety Store')
        ->assertSee('Butterfly Hair Accessories Organizer')
        ->assertSee('Wholesale Price');
});

it('hides internal China pricing on the public product detail page', function () {
    $category = Category::factory()->create(['name' => 'Hair Accessories']);

    Product::factory()->create([
        'category_id' => $category->id,
        'item_number' => 'AVS-000301',
        'barcode' => '8901234567898',
        'name' => 'Hair Clip Set',
        'slug' => 'hair-clip-set',
        'china_price' => 2.50,
        'exchange_rate' => 39.00,
        'wholesale_price' => 150,
        'retail_price' => 180,
        'stock_quantity' => 50,
        'status' => 'active',
    ]);

    $response = $this->get('/products/hair-clip-set');

    $response->assertOk()
        ->assertSee('Hair Clip Set')
        ->assertSee('Wholesale Price')
        ->assertSee('Retail Price')
        ->assertDontSee('China Price')
        ->assertDontSee('¥ 2.50');
});
