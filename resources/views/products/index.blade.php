<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Item Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Items</h1>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $products->count() }} catalog items · active CNY → PKR rate
                            <span class="font-semibold text-slate-700">Rs. {{ number_format($currentRate, 2) }}</span>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('products.search') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Product Search</a>
                        <a href="{{ route('categories.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Categories</a>
                        <a href="{{ route('brands.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Brands</a>
                        <a href="{{ route('products.bulk-create') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Bulk Add</a>
                        <a href="{{ route('products.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">New Item</a>
                    </div>
                </div>
            </div>

            @php
                $filterInputClass = 'w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none';
                $hasFilters = (bool) array_filter($filters);
            @endphp

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Manage Items By Classification</h2>
                        <p class="mt-1 text-sm text-slate-500">Filter the catalog by category, sub-category, and brand. Picking a category narrows the sub-category list.</p>
                    </div>
                    @if ($hasFilters)
                        <a href="{{ route('products.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset Filters</a>
                    @endif
                </div>

                <form method="GET" action="{{ route('products.index') }}" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="filter_category_id" class="mb-2 block text-sm font-medium text-slate-700">Category</label>
                        <select name="category_id" id="filter_category_id" onchange="this.form.submit()" class="{{ $filterInputClass }}">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected($filters['category_id'] === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter_subcategory_id" class="mb-2 block text-sm font-medium text-slate-700">Sub-category</label>
                        <select name="subcategory_id" id="filter_subcategory_id" class="{{ $filterInputClass }}">
                            <option value="">All sub-categories</option>
                            @foreach ($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" @selected($filters['subcategory_id'] === $subcategory->id)>{{ $subcategory->name }}</option>
                            @endforeach
                        </select>
                        @if ($filters['category_id'] && $subcategories->isEmpty())
                            <p class="mt-1 text-xs text-slate-500">This category has no sub-categories yet.</p>
                        @endif
                    </div>

                    <div>
                        <label for="filter_brand_id" class="mb-2 block text-sm font-medium text-slate-700">Brand</label>
                        <select name="brand_id" id="filter_brand_id" class="{{ $filterInputClass }}">
                            <option value="">All brands</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected($filters['brand_id'] === $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter_status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                        <select name="status" id="filter_status" class="{{ $filterInputClass }}">
                            <option value="">All statuses</option>
                            <option value="active" @selected($filters['status'] === 'active')>Active</option>
                            <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-end sm:col-span-2 lg:col-span-4">
                        <button type="submit" class="rounded-md bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Apply Filters</button>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Item</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Category / Sub-category</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Brand</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Cost (PKR)</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Wholesale</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Retail</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Stock</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($products as $product)
                                @php
                                    $image = $product->primaryImage();
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100">
                                                @if ($image)
                                                    <img src="{{ asset($image->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                                @else
                                                    <span class="text-base">📦</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $product->item_number }} · {{ $product->barcode ?: 'no barcode' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        <p class="font-medium text-slate-700">{{ $product->category?->name ?? 'Uncategorised' }}</p>
                                        @if ($product->subcategory)
                                            <p class="text-xs text-slate-500">{{ $product->subcategory->name }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $product->brand?->name ?? 'No brand' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-900">Rs. {{ number_format((float) $product->cost_pkr, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">Rs. {{ number_format((float) $product->wholesale_price, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">Rs. {{ number_format((float) $product->retail_price, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-900">
                                        {{ $product->stock_quantity }} {{ $product->unit }}
                                        <p class="text-xs text-slate-500">MOQ {{ $product->minimum_order_quantity }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($product->status === 'active')
                                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600">Inactive</span>
                                        @endif
                                        @if ($product->is_featured)
                                            <span class="ml-1 inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">Featured</span>
                                        @endif
                                        @if ($product->is_new_arrival)
                                            <span class="ml-1 inline-flex rounded-full bg-sky-100 px-2.5 py-1 text-xs font-semibold text-sky-700">New</span>
                                        @endif
                                        @if ($product->is_sale)
                                            <span class="ml-1 inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Sale</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('products.show', $product) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-500">View</a>
                                            <a href="{{ route('products.edit', $product) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Edit</a>
                                            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this item? It is soft deleted and can be restored later.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-500">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-sm text-slate-500">
                                        @if ($hasFilters)
                                            No items match these filters. <a href="{{ route('products.index') }}" class="font-semibold text-indigo-600">Reset the filters</a>.
                                        @else
                                            No items yet. <a href="{{ route('products.create') }}" class="font-semibold text-indigo-600">Add the first item</a>.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
