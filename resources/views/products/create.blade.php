<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            New Item
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-gradient-to-r from-indigo-600 to-violet-600 p-6 text-white shadow-sm">
                <p class="text-xs uppercase tracking-[0.25em] text-indigo-100">Catalog</p>
                <h1 class="mt-3 text-3xl font-bold">Add Item</h1>
                <p class="mt-2 text-indigo-100">Register a new catalog item with pricing, packaging, and images.</p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Item number <span class="font-semibold">{{ $itemNumber }}</span> will be assigned automatically if the field is left untouched.
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
