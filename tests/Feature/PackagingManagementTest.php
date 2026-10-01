<?php

use App\Models\Product;
use App\Models\User;

it('shows the packaging screen with pack sizes for each item', function () {
    $user = User::factory()->create();

    Product::factory()->create([
        'name' => 'Hair Clip Pack',
        'slug' => 'hair-clip-pack',
        'pieces_per_packet' => 12,
        'pieces_per_box' => 144,
        'pieces_per_carton' => 1728,
    ]);

    $response = $this->actingAs($user)->get(route('inventory.packaging'));

    $response->assertOk()
        ->assertSee('Packaging')
        ->assertSee('Hair Clip Pack')
        ->assertSee('1728')
        ->assertSee('Selling Units');
});

it('updates pack sizes for an item from the packaging screen', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create([
        'name' => 'Gift Pouch',
        'slug' => 'gift-pouch',
        'pieces_per_packet' => 0,
        'pieces_per_box' => 0,
        'pieces_per_carton' => 0,
    ]);

    $response = $this->actingAs($user)->post(route('inventory.pack-sizes'), [
        'product_id' => $product->id,
        'pieces_per_packet' => 10,
        'pieces_per_box' => 120,
        'pieces_per_carton' => 1440,
    ]);

    $response->assertRedirect(route('inventory.packaging'))
        ->assertSessionHas('status', 'Pack sizes updated successfully.');

    $product->refresh();

    expect($product->pieces_per_packet)->toBe(10)
        ->and($product->pieces_per_box)->toBe(120)
        ->and($product->pieces_per_carton)->toBe(1440)
        ->and($product->piecesPerSaleUnit('carton'))->toBe(1440)
        ->and($product->availableSaleUnits())->toHaveKeys(['pcs', 'dozen', 'packet', 'box', 'carton']);
});

it('validates the pack size input', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['name' => 'Beauty Comb', 'slug' => 'beauty-comb']);

    $response = $this->actingAs($user)->post(route('inventory.pack-sizes'), [
        'product_id' => $product->id,
        'pieces_per_packet' => -4,
        'pieces_per_box' => 'many',
        'pieces_per_carton' => 100,
    ]);

    $response->assertSessionHasErrors(['pieces_per_packet', 'pieces_per_box']);
});
