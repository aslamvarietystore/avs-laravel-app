<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Currency &amp; Exchange Rates
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 p-6 text-white shadow-sm lg:col-span-2">
                    <p class="text-xs uppercase tracking-[0.25em] text-amber-100">Settlement Rate</p>
                    @if ($activeRate)
                        <h1 class="mt-3 text-3xl font-bold">
                            1 {{ $activeRate->currency_from }} = Rs. {{ number_format((float) $activeRate->rate, 2) }}
                        </h1>
                        <p class="mt-2 text-amber-100">
                            Active since {{ $activeRate->effective_date?->format('d M Y') }} · created {{ $activeRate->created_at?->diffForHumans() }}
                        </p>
                    @else
                        <h1 class="mt-3 text-3xl font-bold">No active CNY → PKR rate</h1>
                        <p class="mt-2 text-amber-100">Fallback rate Rs. 39.00 is used for pricing until a rate is recorded.</p>
                    @endif
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Rates on record</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $exchangeRates->count() }}</p>
                    <p class="mt-2 text-xs text-slate-500">History is kept so older purchase costs can be recalculated.</p>
                    <a href="{{ route('products.index') }}" class="mt-4 inline-flex rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to Items</a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Rate History</h2>

                    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Pair</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Rate</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Effective</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse ($exchangeRates as $exchangeRate)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-900">
                                            {{ $exchangeRate->currency_from }} → {{ $exchangeRate->currency_to }}
                                            @if ($exchangeRate->notes)
                                                <p class="text-xs font-normal text-slate-500">{{ $exchangeRate->notes }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-900">Rs. {{ number_format((float) $exchangeRate->rate, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600">{{ $exchangeRate->effective_date?->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if ($exchangeRate->is_active)
                                                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600">Historical</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-3">
                                                @if ($exchangeRate->is_active)
                                                    <span class="text-xs text-slate-400">Active rate</span>
                                                @else
                                                    <form method="POST" action="{{ route('exchange-rates.activate', $exchangeRate) }}">
                                                        @csrf
                                                        <button type="submit" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Activate</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('exchange-rates.destroy', $exchangeRate) }}" onsubmit="return confirm('Remove this historical rate?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-500">Remove</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No exchange rates recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Record New Rate</h2>
                    <p class="mt-1 text-sm text-slate-500">Use the AVS purchasing/settlement rate. Activating a rate retires the previous one automatically.</p>

                    <form method="POST" action="{{ route('exchange-rates.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="currency_from" class="mb-2 block text-sm font-medium text-slate-700">From</label>
                                <input type="text" name="currency_from" id="currency_from" value="{{ old('currency_from', 'CNY') }}" required class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase focus:border-indigo-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="currency_to" class="mb-2 block text-sm font-medium text-slate-700">To</label>
                                <input type="text" name="currency_to" id="currency_to" value="{{ old('currency_to', 'PKR') }}" required class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase focus:border-indigo-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="rate" class="mb-2 block text-sm font-medium text-slate-700">Rate (1 CNY = ? PKR)</label>
                            <input type="number" step="0.01" min="0.01" name="rate" id="rate" value="{{ old('rate', $activeRate?->rate ?? '39.00') }}" required class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="effective_date" class="mb-2 block text-sm font-medium text-slate-700">Effective Date</label>
                            <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date', now()->toDateString()) }}" required class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="is_active" class="mb-2 block text-sm font-medium text-slate-700">Activate this rate</label>
                            <select name="is_active" id="is_active" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="1" @selected(old('is_active', '1') === '1')>Yes — use for new pricing</option>
                                <option value="0" @selected(old('is_active') === '0')>No — keep as history only</option>
                            </select>
                        </div>

                        <div>
                            <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">Notes</label>
                            <textarea name="notes" id="notes" rows="2" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                            Save Exchange Rate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
