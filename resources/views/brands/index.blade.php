<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Brand Management
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

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Brands</h1>
                        <p class="mt-1 text-sm text-slate-500">Group items by the brand they are bought under so filtering and reports stay accurate.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('products.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Items</a>
                        <a href="{{ route('categories.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Categories</a>
                        <a href="{{ route('brands.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">New Brand</a>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">All Brands</h2>

                    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Slug</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Items</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse ($brands as $brand)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-900">{{ $brand->name }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600">{{ $brand->slug }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600">
                                            <a href="{{ route('products.index', ['brand_id' => $brand->id]) }}" class="font-semibold text-indigo-600 hover:text-indigo-500">{{ $brand->products_count }}</a>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if ($brand->status === 'active')
                                                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('brands.edit', $brand) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Edit</a>
                                                <form method="POST" action="{{ route('brands.destroy', $brand) }}" onsubmit="return confirm('Delete this brand?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-500">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No brands yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Add Brand</h2>
                    <p class="mt-1 text-sm text-slate-500">Leave the slug empty to generate it from the name.</p>

                    <form method="POST" action="{{ route('brands.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="slug" class="mb-2 block text-sm font-medium text-slate-700">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="auto-generated" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <textarea name="description" id="description" rows="3" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                            <select name="status" id="status" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                                <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                            Save Brand
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
