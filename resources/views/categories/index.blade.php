<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Category Management
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
                        <h1 class="text-2xl font-bold text-slate-900">Categories</h1>
                        <p class="mt-1 text-sm text-slate-500">Group catalog items so search, storefront, and reports stay organised.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('products.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Items</a>
                        <a href="{{ route('categories.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">New Category</a>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">All Categories</h2>

                    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Parent Category</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Slug</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Items</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @php
                                    $orderedCategories = $categories
                                        ->whereNull('parent_id')
                                        ->flatMap(fn ($parent) => collect([$parent])->merge($parent->children));
                                @endphp

                                @forelse ($orderedCategories as $category)
                                    <tr class="{{ $category->isSubcategory() ? 'bg-slate-50' : 'bg-white' }}">
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-900">
                                            @if ($category->isSubcategory())
                                                <span class="pl-5 text-slate-400">&#8627;</span> {{ $category->name }}
                                            @else
                                                {{ $category->name }}
                                                <p class="text-xs font-normal text-slate-500">{{ $category->children->count() }} sub-categories</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-600">{{ $category->parent?->name ?? 'Top-level' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600">{{ $category->slug }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600">
                                            {{ $category->isSubcategory() ? $category->subcategory_products_count : $category->products_count }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if ($category->status === 'active')
                                                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('products.index', $category->isSubcategory() ? ['subcategory_id' => $category->id] : ['category_id' => $category->id]) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-500">Items</a>
                                                <a href="{{ route('categories.edit', $category) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Edit</a>
                                                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-500">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No categories yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Add Category</h2>
                    <p class="mt-1 text-sm text-slate-500">Choose a parent to create a sub-category. Leave the slug empty to generate it from the name.</p>

                    <form method="POST" action="{{ route('categories.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="parent_id" class="mb-2 block text-sm font-medium text-slate-700">Parent Category</label>
                            <select name="parent_id" id="parent_id" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="">None (top-level category)</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}" @selected((int) old('parent_id') === $parent->id)>{{ $parent->name }} ({{ $parent->children_count }} sub-categories)</option>
                                @endforeach
                            </select>
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
                            Save Category
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
