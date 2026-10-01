<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

it('shows the category management screen with existing categories', function () {
    $user = User::factory()->create();
    Category::factory()->create(['name' => 'Imitation Jewellery', 'slug' => 'imitation-jewellery']);

    $response = $this->actingAs($user)->get(route('categories.index'));

    $response->assertOk()
        ->assertSee('Category Management')
        ->assertSee('Imitation Jewellery')
        ->assertSee('Add Category');
});

it('creates a category with an automatic slug', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('categories.store'), [
        'name' => 'Nail Accessories',
        'status' => 'active',
        'description' => 'Nail tools and accessories.',
    ]);

    $response->assertRedirect(route('categories.index'))
        ->assertSessionHas('status', 'Category created successfully.');

    $category = Category::query()->where('name', 'Nail Accessories')->firstOrFail();

    expect($category->slug)->toBe('nail-accessories')
        ->and($category->status)->toBe('active');
});

it('updates a category from the edit screen', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Toys', 'slug' => 'toys', 'status' => 'active']);

    $this->actingAs($user)->get(route('categories.edit', $category))
        ->assertOk()
        ->assertSee('Edit Category');

    $response = $this->actingAs($user)->put(route('categories.update', $category), [
        'name' => 'Kids Toys',
        'slug' => 'kids-toys',
        'status' => 'inactive',
    ]);

    $response->assertRedirect(route('categories.index'))
        ->assertSessionHas('status', 'Category updated successfully.');

    $category->refresh();

    expect($category->name)->toBe('Kids Toys')
        ->and($category->slug)->toBe('kids-toys')
        ->and($category->status)->toBe('inactive');
});

it('refuses to delete a category that still has items', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Gift Items', 'slug' => 'gift-items']);

    Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Gift Box Set',
        'slug' => 'gift-box-set',
    ]);

    $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

    $response->assertRedirect(route('categories.index'))
        ->assertSessionHasErrors('category');

    expect(Category::query()->whereKey($category->id)->exists())->toBeTrue();
});

it('deletes an empty category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Fans', 'slug' => 'fans-spare']);

    $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

    $response->assertRedirect(route('categories.index'))
        ->assertSessionHas('status', 'Category deleted successfully.');

    expect(Category::query()->whereKey($category->id)->exists())->toBeFalse();
});
