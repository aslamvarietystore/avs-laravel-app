<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

it('shows the inventory management screen with product stock overview', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Hair Accessories', 'slug' => 'hair-accessories']);

    Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Premium Hair Clip',
        'slug' => 'premium-hair-clip',
        'stock_quantity' => 8,
        'low_stock_threshold' => 10,
        'status' => 'active',
    ]);

    Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Travel Organizer',
        'slug' => 'travel-organizer',
        'stock_quantity' => 42,
        'low_stock_threshold' => 10,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->get(route('inventory.index'));

    $response->assertOk()
        ->assertSee('Inventory Management')
        ->assertSee('Premium Hair Clip')
        ->assertSee('Travel Organizer')
        ->assertSee('Low Stock')
        ->assertSee('42');
});
