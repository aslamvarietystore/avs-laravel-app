<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

it('shows the bulk item form with the starting item number', function () {
    $user = User::factory()->create();

    Product::factory()->create(['item_number' => 'AVS-000001', 'slug' => 'existing-item']);

    $this->actingAs($user)->get(route('products.bulk-create'))
        ->assertOk()
        ->assertSee('Bulk Add Items')
        ->assertSee('Number of Items')
        ->assertSee('AVS-000002');
});

it('creates a batch of items with sequential item numbers and identical details', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Hair Accessories', 'slug' => 'hair-accessories']);
    $subcategory = Category::factory()->childOf($category)->create(['name' => 'Clips', 'slug' => 'clips']);
    $brand = Brand::factory()->create(['name' => 'Kiss Nail', 'slug' => 'kiss-nail']);

    $response = $this->actingAs($user)->post(route('products.bulk-store'), [
        'start_number' => 'AVS-000100',
        'quantity' => 3,
        'name' => 'Bulk Hair Clip',
        'category_id' => $category->id,
        'subcategory_id' => $subcategory->id,
        'brand_id' => $brand->id,
        'description' => 'Shared description for the batch.',
        'china_price' => 5.00,
        'exchange_rate' => 40.00,
        'wholesale_price' => 250.00,
        'retail_price' => 300.00,
        'minimum_order_quantity' => 6,
        'stock_quantity' => 20,
        'low_stock_threshold' => 5,
        'unit' => 'pcs',
        'pieces_per_packet' => 12,
        'status' => 'active',
        'is_featured' => 1,
    ]);

    $products = Product::query()->where('name', 'Bulk Hair Clip')->orderBy('item_number')->get();

    $response->assertRedirect(route('products.index'))
        ->assertSessionHas('status', '3 items created (AVS-000100 to AVS-000102).');

    expect($products->count())->toBe(3)
        ->and($products->pluck('item_number')->all())->toBe(['AVS-000100', 'AVS-000101', 'AVS-000102'])
        ->and($products->pluck('slug')->unique()->count())->toBe(3);

    $products->each(function (Product $product) use ($category, $subcategory, $brand) {
        expect($product->category_id)->toBe($category->id)
            ->and($product->subcategory_id)->toBe($subcategory->id)
            ->and($product->brand_id)->toBe($brand->id)
            ->and((float) $product->cost_pkr)->toBe(200.00)
            ->and($product->minimum_order_quantity)->toBe(6)
            ->and($product->stock_quantity)->toBe(20)
            ->and($product->is_featured)->toBeTrue()
            ->and($product->is_new_arrival)->toBeFalse()
            ->and($product->stockMovements()->count())->toBe(1)
            ->and($product->stockMovements()->first()->notes)->toBe('Opening stock');
    });
});

it('rejects a batch range that collides with existing item numbers', function () {
    $user = User::factory()->create();
    Product::factory()->create(['item_number' => 'AVS-000101', 'slug' => 'taken-number']);

    $response = $this->actingAs($user)->post(route('products.bulk-store'), [
        'start_number' => 'AVS-000100',
        'quantity' => 3,
        'name' => 'Colliding Batch',
        'china_price' => 5.00,
        'exchange_rate' => 40.00,
        'wholesale_price' => 250.00,
        'retail_price' => 300.00,
        'minimum_order_quantity' => 1,
        'stock_quantity' => 0,
        'low_stock_threshold' => 1,
        'unit' => 'pcs',
        'status' => 'active',
    ]);

    $response->assertSessionHasErrors('start_number');

    $messages = implode(' ', session('errors')->get('start_number'));

    expect($messages)->toContain('AVS-000101')
        ->and(Product::query()->where('name', 'Colliding Batch')->exists())->toBeFalse();
});

it('rejects an invalid starting number or quantity', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('products.bulk-store'), [
        'start_number' => 'ABC',
        'quantity' => 0,
        'name' => 'Invalid Batch',
        'china_price' => 5.00,
        'exchange_rate' => 40.00,
        'wholesale_price' => 250.00,
        'retail_price' => 300.00,
        'minimum_order_quantity' => 1,
        'stock_quantity' => 0,
        'low_stock_threshold' => 1,
        'unit' => 'pcs',
        'status' => 'active',
    ]);

    $response->assertSessionHasErrors(['start_number', 'quantity']);

    expect(Product::query()->where('name', 'Invalid Batch')->exists())->toBeFalse();
});

it('applies uploaded images to every item in the batch', function () {
    Storage::fake('product_images');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('products.bulk-store'), [
        'start_number' => '100',
        'quantity' => 2,
        'name' => 'Bulk Photo Item',
        'china_price' => 4.00,
        'exchange_rate' => 40.00,
        'wholesale_price' => 200.00,
        'retail_price' => 250.00,
        'minimum_order_quantity' => 1,
        'stock_quantity' => 0,
        'low_stock_threshold' => 1,
        'unit' => 'pcs',
        'status' => 'active',
        'images' => [UploadedFile::fake()->create('front.jpg', 120)],
    ]);

    $products = Product::query()->where('name', 'Bulk Photo Item')->orderBy('item_number')->get();

    $response->assertRedirect(route('products.index'));

    expect($products->pluck('item_number')->all())->toBe(['AVS-000100', 'AVS-000101'])
        ->and($products->count())->toBe(2);

    $paths = $products->map(function (Product $product) {
        $image = $product->productImages()->firstOrFail();

        expect($image->is_primary)->toBeTrue()
            ->and($image->sort_order)->toBe(1);

        return $image->image_path;
    });

    expect($paths->unique()->count())->toBe(2)
        ->and($paths->first())->toStartWith('images/products/');

    foreach ($paths as $path) {
        Storage::disk('product_images')->assertExists(Str::after($path, 'images/products/'));
    }
});
