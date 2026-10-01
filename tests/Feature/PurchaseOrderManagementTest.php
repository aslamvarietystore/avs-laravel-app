<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;

it('shows the purchase order management screen with recorded purchase orders', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create([
        'name' => 'Apex Imports',
        'phone' => '03001112233',
        'status' => 'active',
    ]);

    Product::factory()->create([
        'category_id' => Category::factory()->create(['name' => 'Toys', 'slug' => 'toys'])->id,
        'name' => 'Imported Doll',
        'slug' => 'imported-doll',
        'stock_quantity' => 5,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->get(route('purchase-orders.index'));

    $response->assertOk()
        ->assertSee('Purchase Orders')
        ->assertSee('Apex Imports');
});

it('allows staff to create a purchase order and add stock', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create([
        'name' => 'Global Trade',
        'phone' => '03004445566',
        'status' => 'active',
    ]);

    $category = Category::factory()->create(['name' => 'Beauty', 'slug' => 'beauty']);
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Beauty Brush',
        'slug' => 'beauty-brush',
        'stock_quantity' => 10,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post(route('purchase-orders.store'), [
        'supplier_id' => $supplier->id,
        'product_id' => $product->id,
        'quantity' => 20,
        'unit_cost' => 120,
        'notes' => 'Monthly replenishment',
    ]);

    $response->assertRedirect(route('purchase-orders.index'))
        ->assertSessionHas('status', 'Purchase order created successfully.');

    expect($product->fresh()->stock_quantity)->toBe(30)
        ->and($supplier->purchaseOrders()->count())->toBe(1);
});
