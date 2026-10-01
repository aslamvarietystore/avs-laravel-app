<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Brand
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-indigo-600">Brand</p>
                        <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $brand->name }}</h1>
                        <p class="mt-1 text-sm text-slate-500">{{ $itemCount }} items use this brand.</p>
                    </div>
                    <a href="{{ route('brands.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to Brands</a>
                </div>

                <form method="POST" action="{{ route('brands.update', $brand) }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $brand->name) }}" required class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="slug" class="mb-2 block text-sm font-medium text-slate-700">Slug</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $brand->slug) }}" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('description', $brand->description) }}</textarea>
                    </div>

                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                        <select name="status" id="status" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="active" @selected(old('status', $brand->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $brand->status) === 'inactive')>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                        Update Brand
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
