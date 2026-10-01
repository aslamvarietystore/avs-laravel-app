<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cashbook
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
                        <h1 class="text-2xl font-bold text-slate-900">Cashbook</h1>
                        <p class="mt-1 text-sm text-slate-500">Record cash in and cash out movements for the business.</p>
                    </div>
                    <a href="{{ route('cashbook.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        New Entry
                    </a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-slate-900">Cash Movement</h2>
                        <div class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">
                            Balance: Rs. {{ number_format($balance, 2) }}
                        </div>
                    </div>
                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Description</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse ($entries as $entry)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $entry->entry_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $entry->description }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $entry->type === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                {{ $entry->type }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-900">Rs. {{ number_format($entry->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No cashbook entries yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Add Entry</h2>
                    <form method="POST" action="{{ route('cashbook.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="entry_date" class="mb-2 block text-sm font-medium text-slate-700">Date</label>
                            <input type="date" name="entry_date" id="entry_date" value="{{ now()->toDateString() }}" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                        </div>

                        <div>
                            <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <input type="text" name="description" id="description" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="type" class="mb-2 block text-sm font-medium text-slate-700">Type</label>
                                <select name="type" id="type" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                                    <option value="in">Cash In</option>
                                    <option value="out">Cash Out</option>
                                </select>
                            </div>
                            <div>
                                <label for="amount" class="mb-2 block text-sm font-medium text-slate-700">Amount</label>
                                <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                            </div>
                        </div>

                        <div>
                            <label for="category" class="mb-2 block text-sm font-medium text-slate-700">Category</label>
                            <input type="text" name="category" id="category" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                        </div>

                        <div>
                            <label for="reference" class="mb-2 block text-sm font-medium text-slate-700">Reference</label>
                            <input type="text" name="reference" id="reference" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>

                        @if ($errors->any())
                            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                            Save Cashbook Entry
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
