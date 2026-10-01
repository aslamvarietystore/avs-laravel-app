<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Order Entry
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h1 class="text-2xl font-bold text-slate-900">Create Sales Order</h1>

                <form method="POST" action="{{ route('orders.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="customer_id" class="mb-2 block text-sm font-medium text-slate-700">Customer</label>
                            <select name="customer_id" id="customer_id" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="">Select customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="product_id" class="mb-2 block text-sm font-medium text-slate-700">Product</label>
                            <select name="product_id" id="product_id" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="">Select product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock_quantity }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="quantity" class="mb-2 block text-sm font-medium text-slate-700">Quantity</label>
                            <input type="number" name="quantity" id="quantity" min="1" value="1" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="unit_price" class="mb-2 block text-sm font-medium text-slate-700">Unit Price (PKR)</label>
                            <input type="number" name="unit_price" id="unit_price" step="0.01" min="0" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" placeholder="Add order notes"></textarea>
                    </div>

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-md bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-500">
                            Create Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
