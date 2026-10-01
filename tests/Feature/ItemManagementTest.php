<?php

use App\Http\Controllers\ProductController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\Product;
use App\Models\User;
use App\Services\UploadLimitService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

it('shows the item management screen with catalog items', function () {
    $user = User::factory()->create();

    Product::factory()->create([
        'name' => 'Premium Hair Clip',
        'slug' => 'premium-hair-clip',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->get(route('products.index'));

    $response->assertOk()
        ->assertSee('Item Management')
        ->assertSee('Premium Hair Clip')
        ->assertSee('New Item')
        ->assertSee(route('categories.index'))
        ->assertSee(route('inventory.packaging'));
});

it('shows the item form with the next automatic item number', function () {
    $user = User::factory()->create();

    Product::factory()->create(['item_number' => 'AVS-000001', 'slug' => 'existing-item']);

    $response = $this->actingAs($user)->get(route('products.create'));

    $response->assertOk()
        ->assertSee('Add Item')
        ->assertSee('AVS-000002')
        ->assertSee('China Price');
});

it('creates an item with automatic item number, recalculated cost, and opening stock', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Hair Accessories', 'slug' => 'hair-accessories']);
    ExchangeRate::factory()->create(['rate' => 40.00, 'is_active' => true, 'effective_date' => now()->toDateString()]);

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Pearl Hair Pin',
        'category_id' => $category->id,
        'china_price' => 8.50,
        'wholesale_price' => 480.00,
        'retail_price' => 520.00,
        'minimum_order_quantity' => 10,
        'stock_quantity' => 24,
        'low_stock_threshold' => 6,
        'unit' => 'packet',
        'pieces_per_packet' => 12,
        'pieces_per_box' => 144,
        'pieces_per_carton' => 1728,
        'status' => 'active',
    ]);

    $product = Product::query()->where('name', 'Pearl Hair Pin')->firstOrFail();

    $response->assertRedirect(route('products.edit', $product))
        ->assertSessionHas('status', 'Item created successfully.');

    expect($product->item_number)->toBe('AVS-000001')
        ->and($product->slug)->toBe('pearl-hair-pin')
        ->and((float) $product->exchange_rate)->toBe(40.00)
        ->and((float) $product->cost_pkr)->toBe(340.00)
        ->and($product->stock_quantity)->toBe(24)
        ->and($product->category_id)->toBe($category->id)
        ->and($product->pieces_per_carton)->toBe(1728);

    expect($product->stockMovements()->count())->toBe(1)
        ->and($product->stockMovements()->first()->notes)->toBe('Opening stock');
});

it('stores uploaded item images and marks the first one primary', function () {
    Storage::fake('product_images');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Gift Ribbon Roll',
        'china_price' => 2.00,
        'exchange_rate' => 39.00,
        'wholesale_price' => 90.00,
        'retail_price' => 110.00,
        'minimum_order_quantity' => 1,
        'stock_quantity' => 0,
        'low_stock_threshold' => 2,
        'unit' => 'pcs',
        'status' => 'active',
        'images' => [
            UploadedFile::fake()->create('front.jpg', 120),
            UploadedFile::fake()->create('side.jpg', 120),
        ],
    ]);

    $product = Product::query()->where('name', 'Gift Ribbon Roll')->firstOrFail();

    $response->assertRedirect(route('products.edit', $product));

    expect($product->productImages()->count())->toBe(2)
        ->and($product->productImages()->where('is_primary', true)->count())->toBe(1);

    $primary = $product->productImages()->where('is_primary', true)->firstOrFail();

    Storage::disk('product_images')->assertExists(Str::after($primary->image_path, 'images/products/'));
});

it('updates an item, recalculates cost, and records the stock correction', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create([
        'name' => 'Hair Band',
        'slug' => 'hair-band',
        'stock_quantity' => 10,
        'china_price' => 5.00,
        'exchange_rate' => 30.00,
        'cost_pkr' => 150.00,
    ]);

    $originalItemNumber = $product->item_number;

    $response = $this->actingAs($user)->put(route('products.update', $product), [
        'name' => 'Hair Band Deluxe',
        'china_price' => 6.00,
        'exchange_rate' => 40.00,
        'wholesale_price' => 200.00,
        'retail_price' => 250.00,
        'minimum_order_quantity' => 5,
        'stock_quantity' => 16,
        'low_stock_threshold' => 5,
        'unit' => 'pcs',
        'status' => 'active',
    ]);

    $product->refresh();

    $response->assertRedirect(route('products.edit', $product))
        ->assertSessionHas('status', 'Item updated successfully.');

    expect($product->name)->toBe('Hair Band Deluxe')
        ->and($product->slug)->toBe('hair-band-deluxe')
        ->and($product->item_number)->toBe($originalItemNumber)
        ->and((float) $product->cost_pkr)->toBe(240.00)
        ->and($product->stock_quantity)->toBe(16);

    expect($product->stockMovements()->where('type', 'adjustment')->count())->toBe(1);
});

it('removes an item image that is marked for deletion', function () {
    Storage::fake('product_images');

    $user = User::factory()->create();

    $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Nail Art Kit',
        'china_price' => 3.00,
        'exchange_rate' => 39.00,
        'wholesale_price' => 150.00,
        'retail_price' => 180.00,
        'minimum_order_quantity' => 2,
        'stock_quantity' => 0,
        'low_stock_threshold' => 1,
        'unit' => 'pcs',
        'status' => 'active',
        'images' => [UploadedFile::fake()->create('kit.jpg', 80)],
    ]);

    $product = Product::query()->where('name', 'Nail Art Kit')->firstOrFail();
    $image = $product->productImages()->firstOrFail();

    $response = $this->actingAs($user)->put(route('products.update', $product), [
        'name' => 'Nail Art Kit',
        'china_price' => 3.00,
        'exchange_rate' => 39.00,
        'wholesale_price' => 150.00,
        'retail_price' => 180.00,
        'minimum_order_quantity' => 2,
        'stock_quantity' => 0,
        'low_stock_threshold' => 1,
        'unit' => 'pcs',
        'status' => 'active',
        'delete_images' => [$image->id],
    ]);

    $response->assertRedirect(route('products.edit', $product));

    expect($product->productImages()->count())->toBe(0);
    Storage::disk('product_images')->assertMissing(Str::after($image->image_path, 'images/products/'));
});

it('shows the item edit screen with pricing, packaging, and image controls', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create([
        'name' => 'Pearl Hair Pin',
        'slug' => 'pearl-hair-pin',
        'pieces_per_packet' => 12,
        'pieces_per_box' => 144,
        'pieces_per_carton' => 1728,
    ]);

    $product->productImages()->create([
        'image_path' => 'images/default-product.jpg',
        'is_primary' => true,
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($user)->get(route('products.edit', $product));

    $response->assertOk()
        ->assertSee('Edit Item')
        ->assertSee('Pearl Hair Pin')
        ->assertSee('Pieces per Packet')
        ->assertSee('Pieces per Carton')
        ->assertSee('Primary')
        ->assertSee('Save Item');
});

it('soft deletes an item instead of removing the row', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create(['name' => 'Discontinued Clip', 'slug' => 'discontinued-clip']);

    $response = $this->actingAs($user)->delete(route('products.destroy', $product));

    $response->assertRedirect(route('products.index'))
        ->assertSessionHas('status', 'Item deleted successfully.');

    expect(Product::query()->whereKey($product->id)->exists())->toBeFalse()
        ->and(Product::withTrashed()->whereKey($product->id)->exists())->toBeTrue();
});

it('rejects an image larger than the allowed size with a readable message', function () {
    $user = User::factory()->create();
    $service = app(UploadLimitService::class);
    $limitKilobytes = min(ProductController::MAX_IMAGE_KILOBYTES, $service->maxFileKilobytes());

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Oversized Poster',
        'china_price' => 10.00,
        'wholesale_price' => 500.00,
        'retail_price' => 600.00,
        'minimum_order_quantity' => 1,
        'stock_quantity' => 1,
        'low_stock_threshold' => 1,
        'unit' => 'pcs',
        'status' => 'active',
        'images' => [UploadedFile::fake()->create('poster.jpg', $limitKilobytes + 1024, 'image/jpeg')],
    ]);

    $response->assertSessionHasErrors('images.0');

    $messages = implode(' ', session('errors')->get('images.0'));

    expect($messages)->toContain('Each image must be '.$service->formatBytes($limitKilobytes * 1024).' or smaller')
        ->and(Product::query()->where('name', 'Oversized Poster')->exists())->toBeFalse();
});

it('explains a php-level image rejection instead of the raw failed to upload error', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create(['name' => 'Server Rejected Item', 'slug' => 'server-rejected-item']);

    $fake = UploadedFile::fake()->create('photo.jpg', 10, 'image/jpeg');
    $rejected = new UploadedFile($fake->getRealPath(), 'photo.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true);

    $response = $this->actingAs($user)->put(route('products.update', $product), [
        'name' => 'Server Rejected Item',
        'china_price' => 10.00,
        'wholesale_price' => 500.00,
        'retail_price' => 600.00,
        'minimum_order_quantity' => 1,
        'stock_quantity' => 1,
        'low_stock_threshold' => 1,
        'unit' => 'pcs',
        'status' => 'active',
        'images' => [$rejected],
    ]);

    $response->assertSessionHasErrors('images.0');

    $messages = implode(' ', session('errors')->get('images.0'));

    expect($messages)->toContain('upload_max_filesize')
        ->and($messages)->not->toContain('failed to upload');
});

it('shows the effective image upload limits on the item forms', function () {
    $user = User::factory()->create();
    $service = app(UploadLimitService::class);

    $count = min(ProductController::MAX_IMAGES_PER_UPLOAD, $service->maxFileCount());
    $label = $service->formatBytes(min(ProductController::MAX_IMAGE_KILOBYTES, $service->maxFileKilobytes()) * 1024);

    $product = Product::factory()->create(['name' => 'Limit Check Item', 'slug' => 'limit-check-item']);

    $this->actingAs($user)->get(route('products.create'))
        ->assertOk()
        ->assertSee("Upload up to {$count} files at a time")
        ->assertSee("max {$label} each");

    $this->actingAs($user)->get(route('products.edit', $product))
        ->assertOk()
        ->assertSee("Upload up to {$count} files at a time")
        ->assertSee("max {$label} each");
});

it('creates an item with a sub-category, brand, minimum order quantity, and flags', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Hair Accessories', 'slug' => 'hair-accessories']);
    $subcategory = Category::factory()->childOf($category)->create(['name' => 'Clips', 'slug' => 'clips']);
    $brand = Brand::factory()->create(['name' => 'Kiss Nail', 'slug' => 'kiss-nail']);

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Branded Hair Clip',
        'category_id' => $category->id,
        'subcategory_id' => $subcategory->id,
        'brand_id' => $brand->id,
        'china_price' => 4.00,
        'wholesale_price' => 200.00,
        'retail_price' => 250.00,
        'minimum_order_quantity' => 12,
        'stock_quantity' => 10,
        'low_stock_threshold' => 2,
        'unit' => 'pcs',
        'status' => 'active',
        'is_featured' => 1,
        'is_new_arrival' => 1,
    ]);

    $product = Product::query()->where('name', 'Branded Hair Clip')->firstOrFail();

    $response->assertRedirect(route('products.edit', $product))
        ->assertSessionHas('status', 'Item created successfully.');

    expect($product->category_id)->toBe($category->id)
        ->and($product->subcategory_id)->toBe($subcategory->id)
        ->and($product->brand_id)->toBe($brand->id)
        ->and($product->minimum_order_quantity)->toBe(12)
        ->and($product->is_featured)->toBeTrue()
        ->and($product->is_new_arrival)->toBeTrue()
        ->and($product->is_sale)->toBeFalse();
});

it('assigns the parent category automatically when only a sub-category is chosen', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Beauty Accessories', 'slug' => 'beauty-accessories']);
    $subcategory = Category::factory()->childOf($category)->create(['name' => 'Nail Tools', 'slug' => 'nail-tools']);

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Sub-category Only Item',
        'subcategory_id' => $subcategory->id,
        'china_price' => 3.00,
        'wholesale_price' => 150.00,
        'retail_price' => 180.00,
        'minimum_order_quantity' => 1,
        'stock_quantity' => 5,
        'low_stock_threshold' => 1,
        'unit' => 'pcs',
        'status' => 'active',
    ]);

    $product = Product::query()->where('name', 'Sub-category Only Item')->firstOrFail();

    $response->assertRedirect(route('products.edit', $product));

    expect($product->category_id)->toBe($category->id)
        ->and($product->subcategory_id)->toBe($subcategory->id);
});

it('rejects a sub-category that belongs to a different category', function () {
    $user = User::factory()->create();
    $selectedCategory = Category::factory()->create(['name' => 'Toys', 'slug' => 'toys']);
    $otherCategory = Category::factory()->create(['name' => 'Fans', 'slug' => 'fans']);
    $foreignSubcategory = Category::factory()->childOf($otherCategory)->create(['name' => 'Spare Parts', 'slug' => 'spare-parts']);

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Mismatched Item',
        'category_id' => $selectedCategory->id,
        'subcategory_id' => $foreignSubcategory->id,
        'china_price' => 3.00,
        'wholesale_price' => 150.00,
        'retail_price' => 180.00,
        'minimum_order_quantity' => 1,
        'stock_quantity' => 5,
        'low_stock_threshold' => 1,
        'unit' => 'pcs',
        'status' => 'active',
    ]);

    $response->assertSessionHasErrors('subcategory_id');

    expect(Product::query()->where('name', 'Mismatched Item')->exists())->toBeFalse();
});

it('requires a minimum order quantity of at least one', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('products.store'), [
        'name' => 'Zero MOQ Item',
        'china_price' => 3.00,
        'wholesale_price' => 150.00,
        'retail_price' => 180.00,
        'minimum_order_quantity' => 0,
        'stock_quantity' => 5,
        'low_stock_threshold' => 1,
        'unit' => 'pcs',
        'status' => 'active',
    ]);

    $response->assertSessionHasErrors('minimum_order_quantity');

    expect(Product::query()->where('name', 'Zero MOQ Item')->exists())->toBeFalse();
});

it('filters the item list by category, sub-category, and brand', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Gift Items', 'slug' => 'gift-items']);
    $subcategory = Category::factory()->childOf($category)->create(['name' => 'Gift Sets', 'slug' => 'gift-sets']);
    $brand = Brand::factory()->create(['name' => 'Royal Choice', 'slug' => 'royal-choice']);

    Product::factory()->forSubcategory($subcategory)->create([
        'brand_id' => $brand->id,
        'name' => 'Matched Item',
        'slug' => 'matched-item',
    ]);
    Product::factory()->create(['name' => 'Unmatched Item', 'slug' => 'unmatched-item']);

    $this->actingAs($user)->get(route('products.index', ['category_id' => $category->id]))
        ->assertOk()
        ->assertSee('Matched Item')
        ->assertDontSee('Unmatched Item');

    $this->actingAs($user)->get(route('products.index', ['subcategory_id' => $subcategory->id]))
        ->assertOk()
        ->assertSee('Matched Item')
        ->assertDontSee('Unmatched Item');

    $this->actingAs($user)->get(route('products.index', ['brand_id' => $brand->id]))
        ->assertOk()
        ->assertSee('Matched Item')
        ->assertDontSee('Unmatched Item');
});
