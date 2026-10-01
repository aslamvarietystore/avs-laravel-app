<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inventory Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-gradient-to-r from-sky-600 to-cyan-600 p-6 text-white shadow-sm">
                <p class="text-xs uppercase tracking-[0.25em] text-sky-100">Stock Overview</p>
                <h1 class="mt-3 text-3xl font-bold">Inventory Management</h1>
                <p class="mt-2 text-sky-100">Monitor stock levels, identify low inventory, and keep the catalog healthy.</p>
            </div>

            <div class="grid gap-5 md:grid-cols-3">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Total Units</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $totalItems }}</p>
                    <p class="mt-2 text-xs text-slate-500">Across all active products</p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Products</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $products->count() }}</p>
                    <p class="mt-2 text-xs text-slate-500">Catalog items tracked</p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Low Stock</p>
                    <p class="mt-3 text-3xl font-bold text-amber-600">{{ $lowStockItems }}</p>
                    <p class="mt-2 text-xs text-slate-500">Items below threshold</p>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Stock List</h3>
                    <a href="{{ route('products.search') }}" class="text-sm font-medium text-indigo-600">Quick search</a>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Category</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Stock</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Threshold</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($products as $product)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-semibold text-slate-900">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $product->category?->name ?? 'General' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-900">{{ $product->stock_quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $product->low_stock_threshold }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($product->stock_quantity <= $product->low_stock_threshold)
                                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Low Stock</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Healthy</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="bg-slate-50">
                                    <td colspan="5" class="px-4 py-3">
                                        <form method="POST" action="{{ route('inventory.adjust') }}" class="flex flex-col gap-2 md:flex-row md:items-center">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <select name="type" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                                                <option value="purchase">Add stock</option>
                                                <option value="sale">Reduce stock</option>
                                                <option value="adjustment">Adjust stock</option>
                                            </select>
                                            <input type="number" name="quantity" min="1" value="1" class="w-24 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm" />
                                            <select name="direction" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                                                <option value="increase">Increase</option>
                                                <option value="decrease">Decrease</option>
                                            </select>
                                            <input type="text" name="notes" placeholder="Notes" class="w-48 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm" />
                                            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Apply</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No products available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
