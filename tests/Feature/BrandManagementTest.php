<?php

use App\Models\Brand;
use App\Models\Product;
use App\Models\User;

it('shows the brand management screen with existing brands', function () {
    $user = User::factory()->create();
    Brand::factory()->create(['name' => 'Kiss Nail', 'slug' => 'kiss-nail']);

    $response = $this->actingAs($user)->get(route('brands.index'));

    $response->assertOk()
        ->assertSee('Brand Management')
        ->assertSee('Kiss Nail')
        ->assertSee('Add Brand');
});

it('creates a brand with an automatic slug', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('brands.store'), [
        'name' => 'Nail Art Co',
        'status' => 'active',
        'description' => 'Nail art supplies.',
    ]);

    $response->assertRedirect(route('brands.index'))
        ->assertSessionHas('status', 'Brand created successfully.');

    $brand = Brand::query()->where('name', 'Nail Art Co')->firstOrFail();

    expect($brand->slug)->toBe('nail-art-co')
        ->and($brand->status)->toBe('active');
});

it('updates a brand from the edit screen', function () {
    $user = User::factory()->create();
    $brand = Brand::factory()->create(['name' => 'Feora', 'slug' => 'feora', 'status' => 'active']);

    $this->actingAs($user)->get(route('brands.edit', $brand))
        ->assertOk()
        ->assertSee('Edit Brand');

    $response = $this->actingAs($user)->put(route('brands.update', $brand), [
        'name' => 'Feora Beauty',
        'slug' => 'feora-beauty',
        'status' => 'inactive',
    ]);

    $response->assertRedirect(route('brands.index'))
        ->assertSessionHas('status', 'Brand updated successfully.');

    $brand->refresh();

    expect($brand->name)->toBe('Feora Beauty')
        ->and($brand->slug)->toBe('feora-beauty')
        ->and($brand->status)->toBe('inactive');
});

it('refuses to delete a brand that still has items', function () {
    $user = User::factory()->create();
    $brand = Brand::factory()->create(['name' => 'Busy Brand', 'slug' => 'busy-brand']);

    Product::factory()->create([
        'brand_id' => $brand->id,
        'name' => 'Branded Item',
        'slug' => 'branded-item',
    ]);

    $response = $this->actingAs($user)->delete(route('brands.destroy', $brand));

    $response->assertRedirect(route('brands.index'))
        ->assertSessionHasErrors('brand');

    expect(Brand::query()->whereKey($brand->id)->exists())->toBeTrue();
});

it('deletes an empty brand', function () {
    $user = User::factory()->create();
    $brand = Brand::factory()->create(['name' => 'Unused Brand', 'slug' => 'unused-brand']);

    $response = $this->actingAs($user)->delete(route('brands.destroy', $brand));

    $response->assertRedirect(route('brands.index'))
        ->assertSessionHas('status', 'Brand deleted successfully.');

    expect(Brand::query()->whereKey($brand->id)->exists())->toBeFalse();
});
