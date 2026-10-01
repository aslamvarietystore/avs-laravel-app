<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Purchase Orders
            </h2>
            <a href="{{ route('purchase-orders.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                New purchase order
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1.1fr_2fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Suppliers</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($suppliers as $supplier)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $supplier->name }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $supplier->phone ?? 'No phone listed' }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-600">No suppliers recorded yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Recent purchase orders</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($purchaseOrders as $order)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $order->purchase_number }}</p>
                                        <p class="text-sm text-slate-600">{{ $order->supplier->name ?? 'Unknown supplier' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-slate-900">Rs. {{ number_format($order->total, 2) }}</p>
                                        <p class="text-xs capitalize text-slate-500">{{ $order->status }}</p>
                                    </div>
                                </div>
                                @if ($order->items->isNotEmpty())
                                    <div class="mt-3 border-t border-slate-200 pt-3 text-sm text-slate-600">
                                        @foreach ($order->items as $item)
                                            <div class="flex items-center justify-between">
                                                <span>{{ $item->product->name ?? 'Product deleted' }}</span>
                                                <span>{{ $item->quantity }} x Rs. {{ number_format($item->unit_cost, 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-slate-600">No purchase orders recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
