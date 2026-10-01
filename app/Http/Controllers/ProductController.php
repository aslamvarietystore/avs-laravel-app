<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\ItemNumberService;
use App\Services\PricingService;
use App\Services\ProductSearchService;
use App\Services\SlugService;
use App\Services\UploadLimitService;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public const MAX_IMAGES_PER_UPLOAD = 8;

    public const MAX_IMAGE_KILOBYTES = 4096;

    public const MAX_BULK_QUANTITY = 500;

    public function __construct(
        protected PricingService $pricingService,
        protected ItemNumberService $itemNumberService,
        protected SlugService $slugService,
        protected InventoryService $inventoryService,
        protected UploadLimitService $uploadLimitService,
    ) {}

    public function index(Request $request): View
    {
        $categoryId = $this->filterId($request->query('category_id'));
        $subcategoryId = $this->filterId($request->query('subcategory_id'));
        $brandId = $this->filterId($request->query('brand_id'));
        $status = $this->filterStatus($request->query('status'));

        $products = Product::query()
            ->with(['category', 'subcategory', 'brand', 'productImages'])
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($subcategoryId, fn ($query) => $query->where('subcategory_id', $subcategoryId))
            ->when($brandId, fn ($query) => $query->where('brand_id', $brandId))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->get();

        return view('products.index', [
            'products' => $products,
            'currentRate' => $this->pricingService->getCurrentExchangeRate(),
            'categories' => $this->categories(),
            'subcategories' => $this->subcategories($categoryId),
            'brands' => $this->brands(),
            'filters' => [
                'category_id' => $categoryId,
                'subcategory_id' => $subcategoryId,
                'brand_id' => $brandId,
                'status' => $status,
            ],
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'product' => null,
            'categories' => $this->categories(),
            'subcategories' => $this->subcategories(),
            'brands' => $this->brands(),
            'itemNumber' => $this->itemNumberService->nextNumber(),
            'currentRate' => $this->pricingService->getCurrentExchangeRate(),
            'uploadLimits' => $this->uploadLimits(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules($request, null), $this->messages());

        $product = DB::transaction(function () use ($request, $validated): Product {
            $product = Product::query()->create($this->attributes($validated, null, $this->flags($request)));
            $this->storeUploadedImages($request, $product);

            $openingStock = (int) $validated['stock_quantity'];

            if ($openingStock > 0) {
                $this->inventoryService->addStock($product, $openingStock, 'Opening stock', Product::class, $product->id);
            }

            return $product;
        });

        return redirect()
            ->route('products.edit', $product)
            ->with('status', 'Item created successfully.');
    }

    public function bulkCreate(): View
    {
        return view('products.bulk-create', [
            'categories' => $this->categories(),
            'subcategories' => $this->subcategories(),
            'brands' => $this->brands(),
            'currentRate' => $this->pricingService->getCurrentExchangeRate(),
            'uploadLimits' => $this->uploadLimits(),
            'startNumber' => $this->itemNumberService->nextNumber(),
        ]);
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->bulkRules($request), $this->messages());

        $startSequence = $this->bulkStartSequence((string) $validated['start_number']) ?? 0;
        $itemNumbers = $this->bulkItemNumbers($startSequence, (int) $validated['quantity']);

        $existingNumbers = Product::withTrashed()
            ->whereIn('item_number', $itemNumbers)
            ->pluck('item_number')
            ->all();

        if ($existingNumbers !== []) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'start_number' => 'These item numbers already exist: '.implode(', ', $existingNumbers)
                        .'. Change the starting number or quantity.',
                ]);
        }

        $flags = $this->flags($request);

        DB::transaction(function () use ($request, $validated, $itemNumbers, $flags): void {
            $storedImagePaths = $this->storeBulkUploads($request);

            foreach ($itemNumbers as $index => $itemNumber) {
                $row = $validated;
                $row['item_number'] = $itemNumber;

                $product = Product::query()->create($this->attributes($row, null, $flags));

                if ($storedImagePaths !== []) {
                    $this->attachBulkImages($product, $storedImagePaths, $index);
                }

                $openingStock = (int) $validated['stock_quantity'];

                if ($openingStock > 0) {
                    $this->inventoryService->addStock($product, $openingStock, 'Opening stock', Product::class, $product->id);
                }
            }
        });

        $status = sprintf(
            '%d items created (%s to %s).',
            count($itemNumbers),
            $itemNumbers[0],
            $itemNumbers[count($itemNumbers) - 1],
        );

        return redirect()
            ->route('products.index')
            ->with('status', $status);
    }

    public function edit(Product $product): View
    {
        $product->load(['category', 'subcategory', 'brand', 'productImages']);

        return view('products.edit', [
            'product' => $product,
            'categories' => $this->categories(),
            'subcategories' => $this->subcategories(),
            'brands' => $this->brands(),
            'itemNumber' => $product->item_number,
            'currentRate' => $this->pricingService->getCurrentExchangeRate(),
            'uploadLimits' => $this->uploadLimits(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate($this->rules($request, $product), $this->messages());

        DB::transaction(function () use ($request, $validated, $product): void {
            $targetStock = (int) $validated['stock_quantity'];

            $product->update($this->attributes($validated, $product, $this->flags($request)));

            $difference = $targetStock - $product->stock_quantity;

            if ($difference !== 0) {
                $this->inventoryService->adjustStock(
                    $product,
                    $difference,
                    'adjustment',
                    'Stock corrected from item form',
                    Product::class,
                    $product->id,
                );
            }

            $this->storeUploadedImages($request, $product);
            $this->syncImages($request, $product);
        });

        return redirect()
            ->route('products.edit', $product)
            ->with('status', 'Item updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('status', 'Item deleted successfully.');
    }

    public function search(Request $request, ProductSearchService $productSearchService): View
    {
        $query = trim((string) $request->query('query', ''));
        $products = $query !== '' ? $productSearchService->search($query) : collect();

        return view('products.search', compact('query', 'products'));
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'subcategory', 'brand', 'productImages']);

        return view('products.show', compact('product'));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(Request $request, ?Product $product): array
    {
        $productId = $product?->id;
        $imageLimits = $this->uploadLimits();
        $selectedCategoryId = $this->filterId($request->input('category_id'));

        return [
            'item_number' => ['nullable', 'string', 'max:50', Rule::unique('products', 'item_number')->ignore($productId)],
            'barcode' => ['nullable', 'string', 'max:50', Rule::unique('products', 'barcode')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->whereNull('parent_id')],
            'subcategory_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->whereNotNull('parent_id'),
                function (string $attribute, mixed $value, Closure $fail) use ($selectedCategoryId): void {
                    if (blank($value) || $selectedCategoryId === null) {
                        return;
                    }

                    $parentId = Category::query()->whereKey($value)->value('parent_id');

                    if ($parentId === null || (int) $parentId !== $selectedCategoryId) {
                        $fail('Choose a sub-category that belongs to the selected category.');
                    }
                },
            ],
            'brand_id' => ['nullable', 'integer', Rule::exists('brands', 'id')],
            'description' => ['nullable', 'string', 'max:2000'],
            'china_price' => ['required', 'numeric', 'min:0'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'wholesale_price' => ['required', 'numeric', 'min:0'],
            'retail_price' => ['required', 'numeric', 'min:0'],
            'minimum_order_quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'in:pcs,dozen,packet,box,carton'],
            'pieces_per_packet' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'pieces_per_box' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'pieces_per_carton' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_featured' => ['nullable', 'boolean'],
            'is_new_arrival' => ['nullable', 'boolean'],
            'is_sale' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:'.$imageLimits['count']],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.$imageLimits['kilobytes']],
            'primary_image_id' => ['nullable', 'integer'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer'],
            'image_sort_order' => ['nullable', 'array'],
            'image_sort_order.*' => ['integer', 'min:0', 'max:100000'],
        ];
    }

    /**
     * Shared item fields for a batch, plus the item-number range inputs.
     * Item number, barcode, and SKU are dropped because every item in the
     * batch gets its own generated item number and the other two stay empty.
     *
     * @return array<string, array<int, mixed>>
     */
    private function bulkRules(Request $request): array
    {
        $rules = $this->rules($request, null);

        unset($rules['item_number'], $rules['barcode'], $rules['sku']);

        return array_merge($rules, [
            'start_number' => [
                'required',
                'string',
                'max:50',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($this->bulkStartSequence((string) $value) === null) {
                        $fail('Enter a starting item number such as '.ItemNumberService::PREFIX.'000100, or a plain number like 100.');
                    }
                },
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.self::MAX_BULK_QUANTITY],
        ]);
    }

    /**
     * PHP rejects files larger than upload_max_filesize before validation runs,
     * so the "failed to upload" error needs to name the real limit.
     *
     * @return array<string, string>
     */
    private function messages(): array
    {
        $limits = $this->uploadLimits();

        return [
            'subcategory_id.exists' => 'Choose a sub-category that belongs to the selected category.',
            'brand_id.exists' => 'Choose a brand from the list.',
            'minimum_order_quantity.min' => 'Minimum order quantity must be at least 1.',
            'start_number.required' => 'Enter the starting item number for the batch.',
            'quantity.required' => 'Enter how many items to create.',
            'quantity.min' => 'Create at least 1 item at a time.',
            'quantity.max' => 'Create at most '.self::MAX_BULK_QUANTITY.' items at a time.',
            'images.max' => "Upload at most {$limits['count']} images at a time.",
            'images.*.image' => 'Images must be JPG, PNG, or WebP files.',
            'images.*.mimes' => 'Images must be JPG, PNG, or WebP files.',
            'images.*.max' => "Each image must be {$limits['label']} or smaller.",
            'images.*.uploaded' => 'The server rejected this image because it is larger than the PHP upload limit ('.$this->uploadLimitService->maxFileLabel().' per file). Upload a smaller file or raise upload_max_filesize in php.ini.',
        ];
    }

    /**
     * Item image limits, capped by what php.ini will actually accept.
     *
     * @return array{count: int, kilobytes: int, bytes: int, label: string}
     */
    private function uploadLimits(): array
    {
        $kilobytes = min(self::MAX_IMAGE_KILOBYTES, $this->uploadLimitService->maxFileKilobytes());

        return [
            'count' => min(self::MAX_IMAGES_PER_UPLOAD, $this->uploadLimitService->maxFileCount()),
            'kilobytes' => $kilobytes,
            'bytes' => $kilobytes * 1024,
            'label' => $this->uploadLimitService->formatBytes($kilobytes * 1024),
        ];
    }

    /**
     * Top-level categories only; sub-categories are chosen separately.
     *
     * @return Collection<int, Category>
     */
    private function categories()
    {
        return Category::query()->parents()->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Category>
     */
    private function subcategories(?int $categoryId = null)
    {
        return Category::query()
            ->subcategories()
            ->when($categoryId, fn ($query) => $query->where('parent_id', $categoryId))
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Brand>
     */
    private function brands()
    {
        return Brand::query()->orderBy('name')->get();
    }

    /**
     * @return array{is_featured: bool, is_new_arrival: bool, is_sale: bool}
     */
    private function flags(Request $request): array
    {
        return [
            'is_featured' => $request->boolean('is_featured'),
            'is_new_arrival' => $request->boolean('is_new_arrival'),
            'is_sale' => $request->boolean('is_sale'),
        ];
    }

    private function filterId(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function filterStatus(mixed $value): ?string
    {
        return in_array($value, ['active', 'inactive'], true) ? (string) $value : null;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array{is_featured: bool, is_new_arrival: bool, is_sale: bool}  $flags
     * @return array<string, mixed>
     */
    private function attributes(array $validated, ?Product $product, array $flags): array
    {
        $chinaPrice = (float) $validated['china_price'];
        $exchangeRate = (float) ($validated['exchange_rate'] ?? 0);
        $categoryId = $this->filterId($validated['category_id'] ?? null);
        $subcategoryId = $this->filterId($validated['subcategory_id'] ?? null);

        if ($exchangeRate <= 0) {
            $exchangeRate = $this->pricingService->getCurrentExchangeRate();
        }

        if ($categoryId === null && $subcategoryId !== null) {
            $parentId = Category::query()->whereKey($subcategoryId)->value('parent_id');
            $categoryId = $parentId === null ? null : (int) $parentId;
        }

        return [
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId,
            'brand_id' => $this->filterId($validated['brand_id'] ?? null),
            'item_number' => $this->nullableString($validated['item_number'] ?? null)
                ?? $product?->item_number
                ?? $this->itemNumberService->nextNumber(),
            'barcode' => $this->nullableString($validated['barcode'] ?? null),
            'sku' => $this->nullableString($validated['sku'] ?? null),
            'name' => $validated['name'],
            'slug' => $this->slugService->unique($validated['name'], 'products', $product?->id),
            'description' => $validated['description'] ?? null,
            'china_price' => $chinaPrice,
            'exchange_rate' => $exchangeRate,
            'cost_pkr' => $this->pricingService->calculatePkrCost($chinaPrice, $exchangeRate),
            'wholesale_price' => (float) $validated['wholesale_price'],
            'retail_price' => (float) $validated['retail_price'],
            'minimum_order_quantity' => (int) ($validated['minimum_order_quantity'] ?? 1),
            'low_stock_threshold' => (int) $validated['low_stock_threshold'],
            'unit' => $validated['unit'],
            'pieces_per_packet' => (int) ($validated['pieces_per_packet'] ?? 0),
            'pieces_per_box' => (int) ($validated['pieces_per_box'] ?? 0),
            'pieces_per_carton' => (int) ($validated['pieces_per_carton'] ?? 0),
            'status' => $validated['status'],
            'is_featured' => $flags['is_featured'],
            'is_new_arrival' => $flags['is_new_arrival'],
            'is_sale' => $flags['is_sale'],
        ];
    }

    private function nullableString(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * Sequential item numbers for the inclusive batch range.
     *
     * @return array<int, string>
     */
    private function bulkItemNumbers(int $startSequence, int $quantity): array
    {
        return array_map(
            fn (int $offset): string => ItemNumberService::PREFIX
                .str_pad((string) ($startSequence + $offset), 6, '0', STR_PAD_LEFT),
            range(0, $quantity - 1),
        );
    }

    /**
     * Accepts "100", "000100", or "AVS-000100" style starting numbers.
     */
    private function bulkStartSequence(string $value): ?int
    {
        $pattern = '/^(?:'.preg_quote(ItemNumberService::PREFIX, '/').')?(\d{1,10})$/i';

        return preg_match($pattern, trim($value), $matches) === 1 ? (int) $matches[1] : null;
    }

    /**
     * Store the shared uploads once; each batch item links to or copies them.
     *
     * @return array<int, string>
     */
    private function storeBulkUploads(Request $request): array
    {
        $paths = [];

        /** @var array<int, UploadedFile> $files */
        $files = $request->file('images') ?? [];

        foreach ($files as $file) {
            $path = $file->store('', 'product_images');

            if ($path !== false) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * The first item keeps the original uploads; every later item gets its
     * own copies so deleting images on one item does not affect the others.
     *
     * @param  array<int, string>  $storedPaths
     */
    private function attachBulkImages(Product $product, array $storedPaths, int $productIndex): void
    {
        foreach ($storedPaths as $index => $path) {
            $targetPath = $path;

            if ($productIndex > 0) {
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $targetPath = Str::random(40).($extension !== '' ? '.'.$extension : '');

                Storage::disk('product_images')->copy($path, $targetPath);
            }

            $product->productImages()->create([
                'image_path' => 'images/products/'.$targetPath,
                'is_primary' => $index === 0,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function storeUploadedImages(Request $request, Product $product): void
    {
        /** @var array<int, UploadedFile> $files */
        $files = $request->file('images') ?? [];

        if ($files === []) {
            return;
        }

        $sortOrder = (int) $product->productImages()->max('sort_order');
        $hasPrimary = $product->productImages()->where('is_primary', true)->exists();

        foreach ($files as $file) {
            $path = $file->store('', 'product_images');

            if ($path === false) {
                continue;
            }

            $product->productImages()->create([
                'image_path' => 'images/products/'.$path,
                'is_primary' => ! $hasPrimary,
                'sort_order' => $sortOrder + 1,
            ]);

            $sortOrder++;
            $hasPrimary = true;
        }
    }

    private function syncImages(Request $request, Product $product): void
    {
        $deletedIds = array_filter((array) $request->input('delete_images', []));

        foreach ($product->productImages()->whereIn('id', $deletedIds)->get() as $image) {
            Storage::disk('product_images')->delete(Str::after($image->image_path, 'images/products/'));
            $image->delete();
        }

        $sortOrders = (array) $request->input('image_sort_order', []);

        foreach ($sortOrders as $imageId => $sortOrder) {
            $product->productImages()->whereKey((int) $imageId)->update(['sort_order' => (int) $sortOrder]);
        }

        $primaryImageId = $request->input('primary_image_id');

        if ($primaryImageId !== null && $primaryImageId !== '') {
            $product->productImages()->update(['is_primary' => false]);
            $product->productImages()->whereKey((int) $primaryImageId)->update(['is_primary' => true]);
        }
    }
}
