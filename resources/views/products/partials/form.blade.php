@php
    $isEdit = $product !== null;
    $formAction = $isEdit ? route('products.update', $product) : route('products.store');
    $chinaPrice = old('china_price', $product?->china_price ?? 0);
    $exchangeRate = old('exchange_rate', $product?->exchange_rate ?? $currentRate);
    $selectedCategory = old('category_id', $product?->category_id);
    $selectedSubcategory = old('subcategory_id', $product?->subcategory_id);
    $selectedBrand = old('brand_id', $product?->brand_id);
    $selectedUnit = old('unit', $product?->unit ?? 'pcs');
    $selectedStatus = old('status', $product?->status ?? 'active');
    $units = ['pcs' => 'Pieces (pcs)', 'dozen' => 'Dozen (12 pcs)', 'packet' => 'Packet', 'box' => 'Box', 'carton' => 'Carton'];
    $inputClass = 'w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none';
    $subcategoryGroups = $subcategories->groupBy(fn ($subcategory) => $subcategory->parent?->name ?? 'Other')->sortKeys();
    $subcategoryParents = $subcategories->mapWithKeys(fn ($subcategory) => [(string) $subcategory->id => $subcategory->parent_id])->all();
@endphp

<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-lg font-semibold text-slate-900">Identification</h2>
        <p class="mt-1 text-sm text-slate-500">Item number, barcode, and SKU must remain unique across the catalog.</p>

        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="item_number" class="mb-2 block text-sm font-medium text-slate-700">Item Number</label>
                <input type="text" name="item_number" id="item_number" value="{{ old('item_number', $itemNumber) }}" placeholder="AVS-000001" class="{{ $inputClass }}">
                <p class="mt-1 text-xs text-slate-500">Leave untouched to keep the automatic number.</p>
            </div>

            <div>
                <label for="barcode" class="mb-2 block text-sm font-medium text-slate-700">Barcode</label>
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product?->barcode) }}" class="{{ $inputClass }}">
            </div>

            <div>
                <label for="sku" class="mb-2 block text-sm font-medium text-slate-700">SKU</label>
                <input type="text" name="sku" id="sku" value="{{ old('sku', $product?->sku) }}" class="{{ $inputClass }}">
            </div>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
            x-data="{
                categoryId: '{{ $selectedCategory ?? '' }}',
                subcategoryId: '{{ $selectedSubcategory ?? '' }}',
                parentOf: {{ Illuminate\Support\Js::from($subcategoryParents) }},
                syncCategoryFromSubcategory() {
                    const parentId = this.parentOf[this.subcategoryId] ?? null;

                    if (parentId !== null) {
                        this.categoryId = String(parentId);
                    }
                },
                clearInvalidSubcategory() {
                    const parentId = this.parentOf[this.subcategoryId] ?? null;

                    if (parentId !== null && String(parentId) !== String(this.categoryId)) {
                        this.subcategoryId = '';
                    }
                },
            }">
            <div class="sm:col-span-2">
                <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Product Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product?->name) }}" required class="{{ $inputClass }}">
            </div>

            <div>
                <label for="brand_id" class="mb-2 block text-sm font-medium text-slate-700">Brand</label>
                <select name="brand_id" id="brand_id" class="{{ $inputClass }}">
                    <option value="">No brand</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected((int) $selectedBrand === $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">
                    <a href="{{ route('brands.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Manage brands</a>
                </p>
            </div>

            <div>
                <label for="category_id" class="mb-2 block text-sm font-medium text-slate-700">Category</label>
                <select name="category_id" id="category_id" x-model="categoryId" @change="clearInvalidSubcategory()" class="{{ $inputClass }}">
                    <option value="">Uncategorised</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) $selectedCategory === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="subcategory_id" class="mb-2 block text-sm font-medium text-slate-700">Sub-category</label>
                <select name="subcategory_id" id="subcategory_id" x-model="subcategoryId" @change="syncCategoryFromSubcategory()" class="{{ $inputClass }}">
                    <option value="">No sub-category</option>
                    @foreach ($subcategoryGroups as $parentName => $group)
                        <optgroup label="{{ $parentName }}">
                            @foreach ($group as $subcategory)
                                <option value="{{ $subcategory->id }}" @selected((int) $selectedSubcategory === $subcategory->id)>{{ $subcategory->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">Choosing a sub-category selects its parent category automatically.</p>
            </div>

            <div>
                <label for="minimum_order_quantity" class="mb-2 block text-sm font-medium text-slate-700">Minimum Order Quantity</label>
                <input type="number" min="1" name="minimum_order_quantity" id="minimum_order_quantity" value="{{ old('minimum_order_quantity', $product?->minimum_order_quantity ?? 1) }}" required class="{{ $inputClass }}">
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                <select name="status" id="status" class="{{ $inputClass }}">
                    <option value="active" @selected($selectedStatus === 'active')>Active</option>
                    <option value="inactive" @selected($selectedStatus === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                <textarea name="description" id="description" rows="3" class="{{ $inputClass }}">{{ old('description', $product?->description) }}</textarea>
            </div>

            <div class="flex flex-wrap gap-6 sm:col-span-2 lg:col-span-3">
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product?->is_featured)) class="h-4 w-4 rounded border-slate-300 text-indigo-600">
                    <span class="text-sm font-medium text-slate-700">Feature this item on the storefront</span>
                </label>

                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_new_arrival" value="1" @checked(old('is_new_arrival', $product?->is_new_arrival)) class="h-4 w-4 rounded border-slate-300 text-indigo-600">
                    <span class="text-sm font-medium text-slate-700">Mark as new arrival</span>
                </label>

                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_sale" value="1" @checked(old('is_sale', $product?->is_sale)) class="h-4 w-4 rounded border-slate-300 text-indigo-600">
                    <span class="text-sm font-medium text-slate-700">Mark as on sale</span>
                </label>
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200" x-data="{ chinaPrice: Number(@js((float) $chinaPrice)), rate: Number(@js((float) $exchangeRate)) }">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Pricing</h2>
                <p class="mt-1 text-sm text-slate-500">Cost in PKR is recalculated from China price × exchange rate every time this item is saved.</p>
            </div>
            <div class="rounded-xl bg-indigo-50 px-4 py-3 text-right">
                <p class="text-xs uppercase tracking-wide text-slate-500">Cost (PKR)</p>
                <p class="text-xl font-bold text-indigo-700" x-text="'Rs. ' + (chinaPrice * rate).toFixed(2)">Rs. {{ number_format((float) $chinaPrice * (float) $exchangeRate, 2) }}</p>
            </div>
        </div>

        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="china_price" class="mb-2 block text-sm font-medium text-slate-700">China Price (CNY)</label>
                <input type="number" step="0.01" min="0" name="china_price" id="china_price" x-model.number="chinaPrice" value="{{ $chinaPrice }}" required class="{{ $inputClass }}">
            </div>

            <div>
                <label for="exchange_rate" class="mb-2 block text-sm font-medium text-slate-700">Exchange Rate</label>
                <input type="number" step="0.01" min="0" name="exchange_rate" id="exchange_rate" x-model.number="rate" value="{{ $exchangeRate }}" class="{{ $inputClass }}">
                <p class="mt-1 text-xs text-slate-500">Active CNY → PKR today: Rs. {{ number_format($currentRate, 2) }}</p>
            </div>

            <div>
                <label for="wholesale_price" class="mb-2 block text-sm font-medium text-slate-700">Wholesale Price (PKR)</label>
                <input type="number" step="0.01" min="0" name="wholesale_price" id="wholesale_price" value="{{ old('wholesale_price', $product?->wholesale_price ?? 0) }}" required class="{{ $inputClass }}">
            </div>

            <div>
                <label for="retail_price" class="mb-2 block text-sm font-medium text-slate-700">Retail Price (PKR)</label>
                <input type="number" step="0.01" min="0" name="retail_price" id="retail_price" value="{{ old('retail_price', $product?->retail_price ?? 0) }}" required class="{{ $inputClass }}">
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-lg font-semibold text-slate-900">Stock &amp; Packaging</h2>
        <p class="mt-1 text-sm text-slate-500">Stock corrections are recorded in stock movements so the audit trail stays complete. Pack sizes drive packet, box, and carton selling.</p>

        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="stock_quantity" class="mb-2 block text-sm font-medium text-slate-700">Stock (pieces)</label>
                <input type="number" min="0" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product?->stock_quantity ?? 0) }}" required class="{{ $inputClass }}">
            </div>

            <div>
                <label for="low_stock_threshold" class="mb-2 block text-sm font-medium text-slate-700">Low Stock Threshold</label>
                <input type="number" min="0" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', $product?->low_stock_threshold ?? 0) }}" required class="{{ $inputClass }}">
            </div>

            <div>
                <label for="unit" class="mb-2 block text-sm font-medium text-slate-700">Default Selling Unit</label>
                <select name="unit" id="unit" class="{{ $inputClass }}">
                    @foreach ($units as $value => $label)
                        <option value="{{ $value }}" @selected($selectedUnit === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="pieces_per_packet" class="mb-2 block text-sm font-medium text-slate-700">Pieces per Packet</label>
                <input type="number" min="0" name="pieces_per_packet" id="pieces_per_packet" value="{{ old('pieces_per_packet', $product?->pieces_per_packet ?? 0) }}" class="{{ $inputClass }}">
            </div>

            <div>
                <label for="pieces_per_box" class="mb-2 block text-sm font-medium text-slate-700">Pieces per Box</label>
                <input type="number" min="0" name="pieces_per_box" id="pieces_per_box" value="{{ old('pieces_per_box', $product?->pieces_per_box ?? 0) }}" class="{{ $inputClass }}">
            </div>

            <div>
                <label for="pieces_per_carton" class="mb-2 block text-sm font-medium text-slate-700">Pieces per Carton</label>
                <input type="number" min="0" name="pieces_per_carton" id="pieces_per_carton" value="{{ old('pieces_per_carton', $product?->pieces_per_carton ?? 0) }}" class="{{ $inputClass }}">
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-lg font-semibold text-slate-900">Images</h2>
        <p class="mt-1 text-sm text-slate-500">Upload up to {{ $uploadLimits['count'] }} files at a time (JPG, PNG, or WebP, max {{ $uploadLimits['label'] }} each). Choose one primary image and use the sort numbers to reorder the gallery.</p>

        <div class="mt-5" x-data="{ oversized: [] }">
            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="{{ $inputClass }}"
                @change="oversized = Array.from($event.target.files).filter((file) => file.size > {{ $uploadLimits['bytes'] }}).map((file) => file.name)">

            <template x-if="oversized.length">
                <p class="mt-2 text-sm font-medium text-red-600">
                    Too large for this server (max {{ $uploadLimits['label'] }}): <span x-text="oversized.join(', ')"></span>
                </p>
            </template>
        </div>

        @if ($isEdit)
            @if ($product->productImages->isEmpty())
                <p class="mt-4 text-sm text-slate-500">No images uploaded yet.</p>
            @else
                <div class="mt-5 space-y-3">
                    @foreach ($product->productImages as $image)
                        <div class="flex flex-wrap items-center gap-4 rounded-xl border border-slate-200 p-3">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                <img src="{{ asset($image->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            </div>

                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="radio" name="primary_image_id" value="{{ $image->id }}" @checked($image->is_primary) class="h-4 w-4 border-slate-300 text-indigo-600">
                                Primary
                            </label>

                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                Sort
                                <input type="number" min="0" name="image_sort_order[{{ $image->id }}]" value="{{ $image->sort_order }}" class="w-20 rounded-md border border-slate-300 px-2 py-1 text-sm">
                            </label>

                            <label class="flex items-center gap-2 text-sm font-medium text-red-600">
                                <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="h-4 w-4 rounded border-slate-300 text-red-600">
                                Delete
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </section>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('products.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-md bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
            {{ $isEdit ? 'Save Item' : 'Create Item' }}
        </button>
    </div>
</form>
