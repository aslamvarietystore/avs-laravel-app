<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

it('shows key inventory and sales metrics on the reporting dashboard', function () {
    $user = User::factory()->create();

    $hairCategory = Category::factory()->create(['name' => 'Hair Accessories', 'slug' => 'hair-accessories']);
    $travelCategory = Category::factory()->create(['name' => 'Travel Organizers', 'slug' => 'travel-organizers']);

    Product::factory()->create([
        'category_id' => $hairCategory->id,
        'name' => 'Premium Hair Clip',
        'slug' => 'premium-hair-clip',
        'stock_quantity' => 8,
        'status' => 'active',
        'low_stock_threshold' => 10,
    ]);

    Product::factory()->create([
        'category_id' => $travelCategory->id,
        'name' => 'Travel Organizer',
        'slug' => 'travel-organizer',
        'stock_quantity' => 25,
        'status' => 'active',
        'low_stock_threshold' => 10,
    ]);

    Order::factory()->create([
        'order_number' => 'AVS-1001',
        'subtotal' => 500,
        'discount' => 0,
        'shipping_amount' => 50,
        'total' => 550,
        'payment_status' => 'paid',
        'order_status' => 'completed',
    ]);

    Order::factory()->create([
        'order_number' => 'AVS-1002',
        'subtotal' => 300,
        'discount' => 0,
        'shipping_amount' => 20,
        'total' => 320,
        'payment_status' => 'paid',
        'order_status' => 'completed',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Reporting Dashboard')
        ->assertSee('Total Revenue')
        ->assertSee('Low Stock Alerts')
        ->assertSee('870.00')
        ->assertSee('1');
});
