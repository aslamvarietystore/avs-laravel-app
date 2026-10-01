<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;

it('allows staff to create a sales order from the order screen', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Toys', 'slug' => 'toys']);

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Toy Car',
        'slug' => 'toy-car',
        'wholesale_price' => 480,
        'stock_quantity' => 20,
        'status' => 'active',
    ]);

    $customer = Customer::factory()->create([
        'name' => 'Retail Buyer',
        'phone' => '03001234567',
    ]);

    $response = $this->actingAs($user)->post(route('orders.store'), [
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'unit' => 'pcs',
        'unit_price' => 480,
        'payment_method' => 'cash',
        'notes' => 'Wholesale order',
    ]);

    $response->assertRedirect(route('orders.create'))
        ->assertSessionHas('status', 'Order created successfully.');

    expect($product->fresh()->stock_quantity)->toBe(18)
        ->and($customer->orders()->count())->toBe(1)
        ->and($customer->orders()->first()->total)->toBe('960.00');
});
