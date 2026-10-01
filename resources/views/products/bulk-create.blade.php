<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bulk Add Items
        </h2>
    </x-slot>

    @php
        $inputClass = 'w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none';
        $units = ['pcs' => 'Pieces (pcs)', 'dozen' => 'Dozen (12 pcs)', 'packet' => 'Packet', 'box' => 'Box', 'carton' => 'Carton'];
        $selectedUnit = old('unit', 'pcs');
        $subcategoryParents = $subcategories->mapWithKeys(fn ($subcategory) => [(string) $subcategory->id => $subcategory->parent_id])->all();
        $subcategoryGroups = $subcategories->groupBy(fn ($subcategory) => $subcategory->parent?->name ?? 'Other')->sortKeys();
        $maxQuantity = \App\Http\Controllers\ProductController::MAX_BULK_QUANTITY;
    @endphp

    <div class="py-12">
        <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-gradient-to-r from-indigo-600 to-violet-600 p-6 text-white shadow-sm">
                <p class="text-xs uppercase tracking-[0.25em] text-indigo-100">Catalog</p>
                <h1 class="mt-3 text-3xl font-bold">Add Multiple Items</h1>
                <p class="mt-2 text-indigo-100">Enter the details once. Every item in the batch shares them, and only the item number increments across the range you choose.</p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                The batch starts at <span class="font-semibold">{{ $startNumber }}</span> unless you choose another starting number. Up to {{ $maxQuantity }} items can be created at once. Barcode and SKU stay empty for batches — add them per item later if needed.
            </div>

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('products.bulk-store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                    x-data="{
                        start: @js(old('start_number', $startNumber)),
                        quantity: Number(@js((string) old('quantity', 1))),
                        get preview() {
                            const match = String(this.start).trim().match(/(\d+)\s*$/);
                            const base = match ? parseInt(match[1], 10) : NaN;
                            const qty = Number.isFinite(this.quantity) ? Math.trunc(this.quantity) : 0;

                            if (!Number.isFinite(base) || qty < 1) {
                                return null;
                            }

                            const pad = (value) => String(value).padStart(6, '0');

                            return 'AVS-' + pad(base) + ' \u2192 AVS-' + pad(base + qty - 1) + ' \u00b7 ' + qty + ' item' + (qty === 1 ? '' : 's');
                        },
                    }">
                    <h2 class="text-lg font-semibold text-slate-900">Item Number Range</h2>
                    <p class="mt-1 text-sm text-slate-500">Each item receives its own sequential item number. Type a plain number or a full item number such as {{ $startNumber }}.</p>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="start_number" class="mb-2 block text-sm font-medium text-slate-700">Starting Item Number</label>
                            <input type="text" name="start_number" id="start_number" x-model="start" value="{{ old('start_number', $startNumber) }}" required class="{{ $inputClass }}">
                        </div>

                        <div>
                            <label for="quantity" class="mb-2 block text-sm font-medium text-slate-700">Number of Items</label>
                            <input type="number" min="1" max="{{ $maxQuantity }}" name="quantity" id="quantity" x-model.number="quantity" value="{{ old('quantity', 1) }}" required class="{{ $inputClass }}">
                        </div>
                    </div>

                    <p class="mt-3 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700" x-show="preview" x-text="preview"></p>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Shared Details</h2>
                    <p class="mt-1 text-sm text-slate-500">These details are applied to every item created in this batch.</p>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                        x-data="{
                            categoryId: '{{ old('category_id') ?? '' }}',
                            subcategoryId: '{{ old('subcategory_id') ?? '' }}',
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
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="{{ $inputClass }}">
                        </div>

                        <div>
                            <label for="brand_id" class="mb-2 block text-sm font-medium text-slate-700">Brand</label>
                            <select name="brand_id" id="brand_id" class="{{ $inputClass }}">
                                <option value="">No brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected((int) old('brand_id') === $brand->id)>{{ $brand->name }}</option>
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
                                    <option value="{{ $category->id }}" @selected((int) old('category_id') === $category->id)>{{ $category->name }}</option>
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
                                            <option value="{{ $subcategory->id }}" @selected((int) old('subcategory_id') === $subcategory->id)>{{ $subcategory->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Choosing a sub-category selects its parent category automatically.</p>
                        </div>

                        <div>
                            <label for="minimum_order_quantity" class="mb-2 block text-sm font-medium text-slate-700">Minimum Order Quantity</label>
                            <input type="number" min="1" name="minimum_order_quantity" id="minimum_order_quantity" value="{{ old('minimum_order_quantity', 1) }}" required class="{{ $inputClass }}">
                        </div>

                        <div>
                            <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                            <select name="status" id="status" class="{{ $inputClass }}">
                                <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                                <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <textarea name="description" id="description" rows="3" class="{{ $inputClass }}">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex flex-wrap gap-6 sm:col-span-2 lg:col-span-3">
                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured')) class="h-4 w-4 rounded border-slate-300 text-indigo-600">
                                <span class="text-sm font-medium text-slate-700">Feature these items on the storefront</span>
                            </label>

                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="is_new_arrival" value="1" @checked(old('is_new_arrival')) class="h-4 w-4 rounded border-slate-300 text-indigo-600">
                                <span class="text-sm font-medium text-slate-700">Mark as new arrival</span>
                            </label>

                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="is_sale" value="1" @checked(old('is_sale')) class="h-4 w-4 rounded border-slate-300 text-indigo-600">
                                <span class="text-sm font-medium text-slate-700">Mark as on sale</span>
                            </label>
                        </div>
                    </div>
                </section>


                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200" x-data="{ chinaPrice: Number(@js((float) old('china_price', 0))), rate: Number(@js((float) old('exchange_rate', $currentRate))) }">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Pricing</h2>
                            <p class="mt-1 text-sm text-slate-500">Cost in PKR is recalculated from China price × exchange rate for every item in the batch.</p>
                        </div>
                        <div class="rounded-xl bg-indigo-50 px-4 py-3 text-right">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Cost (PKR)</p>
                            <p class="text-xl font-bold text-indigo-700" x-text="'Rs. ' + (chinaPrice * rate).toFixed(2)">Rs. {{ number_format((float) old('china_price', 0) * (float) old('exchange_rate', $currentRate), 2) }}</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="china_price" class="mb-2 block text-sm font-medium text-slate-700">China Price (CNY)</label>
                            <input type="number" step="0.01" min="0" name="china_price" id="china_price" x-model.number="chinaPrice" value="{{ old('china_price', 0) }}" required class="{{ $inputClass }}">
                        </div>

                        <div>
                            <label for="exchange_rate" class="mb-2 block text-sm font-medium text-slate-700">Exchange Rate</label>
                            <input type="number" step="0.01" min="0" name="exchange_rate" id="exchange_rate" x-model.number="rate" value="{{ old('exchange_rate', $currentRate) }}" class="{{ $inputClass }}">
                            <p class="mt-1 text-xs text-slate-500">Active CNY → PKR today: Rs. {{ number_format($currentRate, 2) }}</p>
                        </div>

                        <div>
                            <label for="wholesale_price" class="mb-2 block text-sm font-medium text-slate-700">Wholesale Price (PKR)</label>
                            <input type="number" step="0.01" min="0" name="wholesale_price" id="wholesale_price" value="{{ old('wholesale_price', 0) }}" required class="{{ $inputClass }}">
                        </div>

                        <div>
                            <label for="retail_price" class="mb-2 block text-sm font-medium text-slate-700">Retail Price (PKR)</label>
                            <input type="number" step="0.01" min="0" name="retail_price" id="retail_price" value="{{ old('retail_price', 0) }}" required class="{{ $inputClass }}">
                        </div>
                    </div>
                </section>


                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Stock &amp; Packaging</h2>
                    <p class="mt-1 text-sm text-slate-500">The stock figure becomes the opening stock of every item in the batch, recorded as its own stock movement.</p>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label for="stock_quantity" class="mb-2 block text-sm font-medium text-slate-700">Stock (pieces)</label>
                            <input type="number" min="0" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 0) }}" required class="{{ $inputClass }}">
                        </div>

                        <div>
                            <label for="low_stock_threshold" class="mb-2 block text-sm font-medium text-slate-700">Low Stock Threshold</label>
                            <input type="number" min="0" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', 0) }}" required class="{{ $inputClass }}">
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
                            <input type="number" min="0" name="pieces_per_packet" id="pieces_per_packet" value="{{ old('pieces_per_packet', 0) }}" class="{{ $inputClass }}">
                        </div>

                        <div>
                            <label for="pieces_per_box" class="mb-2 block text-sm font-medium text-slate-700">Pieces per Box</label>
                            <input type="number" min="0" name="pieces_per_box" id="pieces_per_box" value="{{ old('pieces_per_box', 0) }}" class="{{ $inputClass }}">
                        </div>

                        <div>
                            <label for="pieces_per_carton" class="mb-2 block text-sm font-medium text-slate-700">Pieces per Carton</label>
                            <input type="number" min="0" name="pieces_per_carton" id="pieces_per_carton" value="{{ old('pieces_per_carton', 0) }}" class="{{ $inputClass }}">
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Images (optional)</h2>
                    <p class="mt-1 text-sm text-slate-500">Upload up to {{ $uploadLimits['count'] }} files at a time (JPG, PNG, or WebP, max {{ $uploadLimits['label'] }} each). The same images are applied to every item in the batch — the first image becomes each item's primary image.</p>

                    <div class="mt-5" x-data="{ oversized: [] }">
                        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="{{ $inputClass }}"
                            @change="oversized = Array.from($event.target.files).filter((file) => file.size > {{ $uploadLimits['bytes'] }}).map((file) => file.name)">

                        <template x-if="oversized.length">
                            <p class="mt-2 text-sm font-medium text-red-600">
                                Too large for this server (max {{ $uploadLimits['label'] }}): <span x-text="oversized.join(', ')"></span>
                            </p>
                        </template>
                    </div>
                </section>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('products.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="rounded-md bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                        Create Items
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
