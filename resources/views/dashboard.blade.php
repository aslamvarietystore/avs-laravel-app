<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reporting Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-gradient-to-r from-indigo-600 to-sky-600 p-6 text-white shadow-sm">
                <p class="text-xs uppercase tracking-[0.25em] text-indigo-100">Aslam Variety Store</p>
                <h1 class="mt-3 text-3xl font-bold">Reporting Dashboard</h1>
                <p class="mt-2 text-indigo-100">Track sales, inventory health, and operational performance in one place.</p>
            </div>

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Total Revenue</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">Rs. {{ number_format($totalRevenue, 2) }}</p>
                    <p class="mt-2 text-xs text-slate-500">Completed orders</p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Total Orders</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $totalOrders }}</p>
                    <p class="mt-2 text-xs text-slate-500">Across all statuses</p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Active Products</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $activeProducts }}</p>
                    <p class="mt-2 text-xs text-slate-500">Current catalog items</p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Low Stock Alerts</p>
                    <p class="mt-3 text-3xl font-bold text-amber-600">{{ $lowStockItems }}</p>
                    <p class="mt-2 text-xs text-slate-500">Items below threshold</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Recent Orders</h3>
                        <a href="{{ route('products.search') }}" class="text-sm font-medium text-indigo-600">Open search</a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($recentOrders as $order)
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $order->order_number }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-900">Rs. {{ number_format($order->total, 2) }}</p>
                                    <p class="text-xs capitalize text-slate-500">{{ $order->order_status }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-600">No orders yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Quick Action</h3>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('products.search') }}" class="block rounded-xl bg-indigo-600 px-4 py-3 text-center font-semibold text-white hover:bg-indigo-500">
                            Product Search
                        </a>
                        <a href="{{ route('inventory.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Inventory Management
                        </a>
                        <a href="{{ route('customers.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Customer Management
                        </a>
                        <a href="{{ route('orders.create') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Create Order
                        </a>
                        <a href="{{ route('purchase-orders.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Purchase Orders
                        </a>
                        <a href="{{ route('vendors.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Vendor Management
                        </a>
                        <a href="{{ route('expenses.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Expense Management
                        </a>
                        <a href="{{ route('payments.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Payment Management
                        </a>
                        <a href="{{ route('invoices.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Invoice Management
                        </a>
                        <a href="{{ route('quotations.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Quotation Management
                        </a>
                        <a href="{{ route('receivables.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Receivables Management
                        </a>
                        <a href="{{ route('payables.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Payables Management
                        </a>
                        <a href="{{ route('returns.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Return Management
                        </a>
                        <a href="{{ route('cashbook.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Cashbook
                        </a>
                        <a href="{{ route('ledger.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            General Ledger
                        </a>
                        <a href="{{ route('payroll.index') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Staff Payroll
                        </a>
                        <a href="{{ route('storefront.products') }}" class="block rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">
                            Public Catalog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
