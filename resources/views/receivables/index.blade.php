<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Receivables Management
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
                        <h1 class="text-2xl font-bold text-slate-900">Receivables Management</h1>
                        <p class="mt-1 text-sm text-slate-500">Monitor customer balances and outstanding collections.</p>
                    </div>
                    <a href="{{ route('receivables.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        New Receivable
                    </a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Outstanding Balances</h2>
                    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Invoice</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Balance</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse ($receivables as $receivable)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $receivable->customer?->name ?? 'Unknown Customer' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $receivable->invoice_number }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-900">Rs. {{ number_format($receivable->balance, 2) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $receivable->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($receivable->status === 'overdue' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                                {{ $receivable->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No receivable records yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Record Collection</h2>
                    <form method="POST" action="{{ route('receivables.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="customer_id" class="mb-2 block text-sm font-medium text-slate-700">Customer</label>
                            <select name="customer_id" id="customer_id" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                                <option value="">Select customer</option>
                                @foreach ($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="invoice_number" class="mb-2 block text-sm font-medium text-slate-700">Invoice Number</label>
                            <input type="text" name="invoice_number" id="invoice_number" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="amount_due" class="mb-2 block text-sm font-medium text-slate-700">Amount Due</label>
                                <input type="number" step="0.01" min="0.01" name="amount_due" id="amount_due" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                            </div>
                            <div>
                                <label for="amount_received" class="mb-2 block text-sm font-medium text-slate-700">Amount Received</label>
                                <input type="number" step="0.01" min="0" name="amount_received" id="amount_received" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="balance" class="mb-2 block text-sm font-medium text-slate-700">Balance</label>
                                <input type="number" step="0.01" min="0" name="balance" id="balance" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                            </div>
                            <div>
                                <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                                <select name="status" id="status" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                                    <option value="paid">Paid</option>
                                    <option value="partial" selected>Partial</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="due_date" class="mb-2 block text-sm font-medium text-slate-700">Due Date</label>
                            <input type="date" name="due_date" id="due_date" value="{{ now()->addDays(7)->toDateString() }}" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                        </div>

                        @if ($errors->any())
                            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                            Save Receivable
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
