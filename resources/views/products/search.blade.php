<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            AVS Product Search
        </h2>
    </x-slot>

    @php
        $pricingService = app(App\Services\PricingService::class);
        $currentRate = $pricingService->getCurrentExchangeRate();
    @endphp

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-700 px-6 py-6 text-white">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-300">Aslam Variety Store</p>
                    <h1 class="mt-2 text-3xl font-bold">Product Search</h1>
                </div>

                <div class="p-6">
                    <form method="GET" action="{{ route('products.search') }}" class="flex flex-col gap-4 md:flex-row md:items-center">
                        <input
                            type="text"
                            name="query"
                            value="{{ $query }}"
                            autofocus
                            placeholder="Scan Barcode / Enter Item Number"
                            class="w-full rounded-lg border border-slate-300 px-5 py-3 text-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow hover:bg-indigo-500">
                            SEARCH
                        </button>
                    </form>
                </div>
            </div>

            @if($query !== '')
                <div class="mt-8 space-y-6">
                    @if($products->isEmpty())
                        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-600">
                            No product matched “{{ $query }}”. Try a barcode, item number, or product name.
                        </div>
                    @else
                        @foreach($products as $product)
                            @php
                                $costPkr = $pricingService->calculatePkrCost($product->china_price, $product->exchange_rate ?: $currentRate);
                                $primaryImage = $product->primaryImage();
                            @endphp
                            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="flex flex-col gap-6 md:flex-row">
                                    <div class="flex h-40 w-full max-w-[180px] items-center justify-center overflow-hidden rounded-xl bg-slate-100">
                                        @if($primaryImage)
                                            <img src="{{ asset($primaryImage->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="text-center text-sm text-slate-500">
                                                <div class="text-4xl">📦</div>
                                                <div class="mt-2">No Image</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                            <div>
                                                <h2 class="text-2xl font-bold text-slate-900">{{ $product->name }}</h2>
                                                <p class="mt-1 text-sm text-slate-500">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                                            </div>
                                            <a href="{{ route('products.show', $product) }}" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                                                Quick View
                                            </a>
                                        </div>

                                        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                            <div>
                                                <p class="text-xs uppercase tracking-wide text-slate-500">Item No</p>
                                                <p class="mt-1 text-lg font-semibold text-slate-800">{{ $product->item_number }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs uppercase tracking-wide text-slate-500">Barcode</p>
                                                <p class="mt-1 text-lg font-semibold text-slate-800">{{ $product->barcode }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs uppercase tracking-wide text-slate-500">China Price</p>
                                                <p class="mt-1 text-lg font-semibold text-amber-600">¥ {{ number_format($product->china_price, 2) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs uppercase tracking-wide text-slate-500">Stock</p>
                                                <p class="mt-1 text-lg font-semibold text-emerald-600">{{ $product->stock_quantity }} pcs</p>
                                            </div>
                                        </div>

                                        <div class="mt-5 grid gap-4 md:grid-cols-3">
                                            <div class="rounded-xl bg-slate-50 p-4">
                                                <p class="text-xs uppercase tracking-wide text-slate-500">Exchange Rate</p>
                                                <p class="mt-2 text-lg font-bold text-slate-900">1 CNY = Rs. {{ number_format($product->exchange_rate ?: $currentRate, 2) }}</p>
                                            </div>
                                            <div class="rounded-xl bg-slate-50 p-4">
                                                <p class="text-xs uppercase tracking-wide text-slate-500">PKR Cost</p>
                                                <p class="mt-2 text-lg font-bold text-indigo-700">Rs. {{ number_format($costPkr, 2) }}</p>
                                            </div>
                                            <div class="rounded-xl bg-slate-50 p-4">
                                                <p class="text-xs uppercase tracking-wide text-slate-500">Wholesale</p>
                                                <p class="mt-2 text-lg font-bold text-slate-900">Rs. {{ number_format($product->wholesale_price, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
