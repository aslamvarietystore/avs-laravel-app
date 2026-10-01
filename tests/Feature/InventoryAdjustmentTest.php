<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

it('allows staff to adjust product stock from the inventory screen', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Hair Accessories', 'slug' => 'hair-accessories']);

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Premium Hair Clip',
        'slug' => 'premium-hair-clip',
        'stock_quantity' => 8,
        'low_stock_threshold' => 10,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->from(route('inventory.index'))->post(route('inventory.adjust'), [
        'product_id' => $product->id,
        'quantity' => 12,
        'type' => 'purchase',
        'notes' => 'Bulk restock',
    ]);

    $response->assertRedirect(route('inventory.index'))
        ->assertSessionHas('status', 'Stock updated successfully.');

    expect($product->fresh()->stock_quantity)->toBe(20)
        ->and($product->stockMovements()->latest()->first()->notes)->toBe('Bulk restock');
});
