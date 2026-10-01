<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Expense Management
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
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Expense Management</h1>
                        <p class="mt-1 text-sm text-slate-500">Track outgoing operating costs and vendor payments.</p>
                    </div>
                    <a href="{{ route('expenses.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        New Expense
                    </a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Recent Expenses</h2>
                    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Vendor</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Category</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse ($expenses as $expense)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $expense->vendor?->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $expense->category }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-900">Rs. {{ number_format($expense->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 capitalize">{{ $expense->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No expenses recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Add Expense</h2>
                    <form method="POST" action="{{ route('expenses.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="vendor_id" class="mb-2 block text-sm font-medium text-slate-700">Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="">No vendor</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="category" class="mb-2 block text-sm font-medium text-slate-700">Category</label>
                            <input type="text" name="category" id="category" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                        </div>

                        <div>
                            <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <input type="text" name="description" id="description" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="amount" class="mb-2 block text-sm font-medium text-slate-700">Amount</label>
                                <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                            </div>
                            <div>
                                <label for="incurred_on" class="mb-2 block text-sm font-medium text-slate-700">Date</label>
                                <input type="date" name="incurred_on" id="incurred_on" value="{{ now()->toDateString() }}" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                            </div>
                        </div>

                        <div>
                            <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                            <select name="status" id="status" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>

                        <div>
                            <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"></textarea>
                        </div>

                        @if ($errors->any())
                            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                            Save Expense
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
