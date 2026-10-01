<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Product Details
        </h2>
    </x-slot>

    @php
        $pricingService = app(App\Services\PricingService::class);
        $rate = $product->exchange_rate ?: $pricingService->getCurrentExchangeRate();
        $costPkr = $pricingService->calculatePkrCost($product->china_price, $rate);
        $primaryImage = $product->primaryImage();
        $isAuthenticated = auth()->check();
    @endphp

    <div class="py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="grid gap-8 p-8 lg:grid-cols-2">
                    <div class="flex items-center justify-center rounded-2xl bg-slate-100 p-4">
                        @if($primaryImage)
                            <img src="{{ asset($primaryImage->image_path) }}" alt="{{ $product->name }}" class="max-h-[420px] object-contain">
                        @else
                            <div class="text-center text-slate-500">
                                <div class="text-6xl">📦</div>
                                <p class="mt-3 text-lg">No Product Image</p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-indigo-600">Product</p>
                        <h1 class="mt-3 text-3xl font-bold text-slate-900">{{ $product->name }}</h1>

                        <dl class="mt-6 space-y-3 text-sm text-slate-700">
                            <div class="flex justify-between border-b border-slate-200 pb-2">
                                <dt>Item No</dt>
                                <dd class="font-semibold">{{ $product->item_number }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-2">
                                <dt>Barcode</dt>
                                <dd class="font-semibold">{{ $product->barcode }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-2">
                                <dt>Category</dt>
                                <dd class="font-semibold">{{ $product->category?->name ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-2">
                                <dt>Sub-category</dt>
                                <dd class="font-semibold">{{ $product->subcategory?->name ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-2">
                                <dt>Brand</dt>
                                <dd class="font-semibold">{{ $product->brand?->name ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-2">
                                <dt>Minimum Order Qty</dt>
                                <dd class="font-semibold">{{ $product->minimum_order_quantity }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-2">
                                <dt>Stock</dt>
                                <dd class="font-semibold">{{ $product->stock_quantity }} pcs</dd>
                            </div>
                        </dl>

                        @if ($product->is_featured || $product->is_new_arrival || $product->is_sale)
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if ($product->is_featured)
                                    <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Featured</span>
                                @endif
                                @if ($product->is_new_arrival)
                                    <span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">New Arrival</span>
                                @endif
                                @if ($product->is_sale)
                                    <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">On Sale</span>
                                @endif
                            </div>
                        @endif

                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            @if ($isAuthenticated)
                                <div class="rounded-xl bg-amber-50 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">China Price</p>
                                    <p class="mt-2 text-2xl font-bold text-amber-600">¥ {{ number_format($product->china_price, 2) }}</p>
                                </div>
                                <div class="rounded-xl bg-indigo-50 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">Current Rate</p>
                                    <p class="mt-2 text-2xl font-bold text-indigo-700">Rs. {{ number_format($rate, 2) }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-100 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">PKR Cost</p>
                                    <p class="mt-2 text-2xl font-bold text-slate-900">Rs. {{ number_format($costPkr, 2) }}</p>
                                </div>
                            @else
                                <div class="rounded-xl bg-indigo-50 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">Current Exchange Rate</p>
                                    <p class="mt-2 text-2xl font-bold text-indigo-700">Rs. {{ number_format($rate, 2) }}</p>
                                </div>
                            @endif
                            <div class="rounded-xl bg-emerald-50 p-4">
                                <p class="text-xs uppercase tracking-wide text-slate-500">Wholesale Price</p>
                                <p class="mt-2 text-2xl font-bold text-emerald-700">Rs. {{ number_format($product->wholesale_price, 2) }}</p>
                            </div>
                            <div class="rounded-xl bg-sky-50 p-4">
                                <p class="text-xs uppercase tracking-wide text-slate-500">Retail Price</p>
                                <p class="mt-2 text-2xl font-bold text-sky-700">Rs. {{ number_format($product->retail_price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
