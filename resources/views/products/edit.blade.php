<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Item
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-indigo-600">{{ $product->item_number }}</p>
                        <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $product->name }}</h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Stock {{ $product->stock_quantity }} pcs · Cost Rs. {{ number_format((float) $product->cost_pkr, 2) }} · Rate Rs. {{ number_format((float) $product->exchange_rate, 2) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('products.show', $product) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">View</a>
                        <a href="{{ route('inventory.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Inventory</a>
                    </div>
                </div>
            </div>

            @include('products.partials.form', [
                'product' => $product,
                'categories' => $categories,
                'subcategories' => $subcategories,
                'brands' => $brands,
                'itemNumber' => $itemNumber,
                'currentRate' => $currentRate,
                'uploadLimits' => $uploadLimits,
            ])
        </div>
    </div>
</x-app-layout>
