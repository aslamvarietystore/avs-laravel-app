<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Packaging &amp; Pack Sizes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
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

            <div class="rounded-2xl border border-slate-200 bg-gradient-to-r from-teal-600 to-emerald-600 p-6 text-white shadow-sm">
                <p class="text-xs uppercase tracking-[0.25em] text-teal-100">Catalog</p>
                <h1 class="mt-3 text-3xl font-bold">Packaging &amp; Pack Sizes</h1>
                <p class="mt-2 text-teal-100">Define how many pieces sit inside a packet, a box, and a carton so orders can be captured in selling units.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('inventory.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Stock List</a>
                <a href="{{ route('products.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Items</a>
                <a href="{{ route('products.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">New Item</a>
                <span class="text-sm text-slate-500">Products tracked: <span class="font-semibold text-slate-700">{{ $products->count() }}</span></span>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Item</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Selling Unit</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Pieces / Packet</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Pieces / Box</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Pieces / Carton</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Save</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($products as $product)
                                @php
                                    $packetsPerBox = ($product->pieces_per_packet > 0 && $product->pieces_per_box >= $product->pieces_per_packet)
                                        ? intdiv($product->pieces_per_box, $product->pieces_per_packet)
                                        : null;
                                    $boxesPerCarton = ($product->pieces_per_box > 0 && $product->pieces_per_carton >= $product->pieces_per_box)
                                        ? intdiv($product->pieces_per_carton, $product->pieces_per_box)
                                        : null;
                                    $fieldClass = 'w-28 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none';
                                @endphp

                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $product->item_number }} · {{ $product->category?->name ?? 'Uncategorised' }}</p>
                                        @if ($packetsPerBox || $boxesPerCarton)
                                            <p class="mt-1 text-xs text-slate-500">
                                                @if ($packetsPerBox)
                                                    {{ $packetsPerBox }} packets / box
                                                @endif
                                                @if ($packetsPerBox && $boxesPerCarton)
                                                    ·
                                                @endif
                                                @if ($boxesPerCarton)
                                                    {{ $boxesPerCarton }} boxes / carton
                                                @endif
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $product->unit }}</span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <input type="number" min="0" name="pieces_per_packet" form="pack-sizes-{{ $product->id }}" value="{{ $product->pieces_per_packet }}" class="{{ $fieldClass }}">
                                    </td>

                                    <td class="px-4 py-3">
                                        <input type="number" min="0" name="pieces_per_box" form="pack-sizes-{{ $product->id }}" value="{{ $product->pieces_per_box }}" class="{{ $fieldClass }}">
                                    </td>

                                    <td class="px-4 py-3">
                                        <input type="number" min="0" name="pieces_per_carton" form="pack-sizes-{{ $product->id }}" value="{{ $product->pieces_per_carton }}" class="{{ $fieldClass }}">
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('products.edit', $product) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Edit Item</a>
                                            <button type="submit" form="pack-sizes-{{ $product->id }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Save</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                        No products available. <a href="{{ route('products.create') }}" class="font-semibold text-indigo-600">Add an item</a> first.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @foreach ($products as $product)
                <form id="pack-sizes-{{ $product->id }}" method="POST" action="{{ route('inventory.pack-sizes') }}" class="hidden">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                </form>
            @endforeach

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Selling Units</h2>
                <ul class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2 lg:grid-cols-3">
                    <li><span class="font-semibold text-slate-900">pcs</span> — single piece, always 1</li>
                    <li><span class="font-semibold text-slate-900">dozen</span> — 12 pieces, always 12</li>
                    <li><span class="font-semibold text-slate-900">packet</span> — pieces per packet</li>
                    <li><span class="font-semibold text-slate-900">box</span> — pieces per box</li>
                    <li><span class="font-semibold text-slate-900">carton</span> — pieces per carton</li>
                    <li>Values of 0 hide the unit on the order screen.</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
