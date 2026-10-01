<?php

use App\Models\Product;
use App\Models\ReturnRecord;
use App\Models\User;

it('shows the return management screen with recorded returns', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Returnable Basket',
        'slug' => 'returnable-basket',
        'stock_quantity' => 12,
        'status' => 'active',
    ]);

    ReturnRecord::query()->create([
        'product_id' => $product->id,
        'quantity' => 2,
        'amount_refunded' => 240,
        'reason' => 'Customer changed mind',
        'returned_on' => now()->toDateString(),
        'status' => 'approved',
    ]);

    $response = $this->actingAs($user)->get(route('returns.index'));

    $response->assertOk()
        ->assertSee('Return Management')
        ->assertSee('Returnable Basket');
});

it('allows staff to create a return record and restore stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Portable Lamp',
        'slug' => 'portable-lamp',
        'stock_quantity' => 10,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post(route('returns.store'), [
        'product_id' => $product->id,
        'quantity' => 3,
        'amount_refunded' => 420,
        'reason' => 'Damaged item',
        'returned_on' => now()->toDateString(),
        'status' => 'approved',
    ]);

    $response->assertRedirect(route('returns.index'))
        ->assertSessionHas('status', 'Return recorded successfully.');

    expect($product->fresh()->stock_quantity)->toBe(13)
        ->and(ReturnRecord::query()->where('reason', 'Damaged item')->count())->toBe(1);
});
