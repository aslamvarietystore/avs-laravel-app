<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Purchase Order
            </h2>
            <a href="{{ route('purchase-orders.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Back to list
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <form action="{{ route('purchase-orders.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="supplier_id" class="mb-2 block text-sm font-medium text-slate-700">Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="product_id" class="mb-2 block text-sm font-medium text-slate-700">Product</label>
                        <select id="product_id" name="product_id" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->stock_quantity }} in stock)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="quantity" class="mb-2 block text-sm font-medium text-slate-700">Quantity</label>
                            <input id="quantity" name="quantity" type="number" min="1" value="1" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        <div>
                            <label for="unit_cost" class="mb-2 block text-sm font-medium text-slate-700">Unit Cost</label>
                            <input id="unit_cost" name="unit_cost" type="number" min="0.01" step="0.01" value="0" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional order notes"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-500">
                            Save Purchase Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
