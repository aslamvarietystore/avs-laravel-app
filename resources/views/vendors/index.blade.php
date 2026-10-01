<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Vendor Management
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
                        <h1 class="text-2xl font-bold text-slate-900">Vendor Management</h1>
                        <p class="mt-1 text-sm text-slate-500">Track supplier records and purchasing relationships.</p>
                    </div>
                    <a href="{{ route('vendors.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        New Vendor
                    </a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Vendors</h2>
                    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">City</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse ($vendors as $vendor)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-900">{{ $vendor->name }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600">{{ $vendor->phone ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600">{{ $vendor->city ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">{{ $vendor->status ?? 'active' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No vendors found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Add Vendor</h2>
                    <form method="POST" action="{{ route('vendors.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                            <input type="text" name="name" id="name" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                        </div>

                        <div>
                            <label for="contact_person" class="mb-2 block text-sm font-medium text-slate-700">Contact Person</label>
                            <input type="text" name="contact_person" id="contact_person" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">Phone</label>
                                <input type="text" name="phone" id="phone" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                                <input type="email" name="email" id="email" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="company_name" class="mb-2 block text-sm font-medium text-slate-700">Company</label>
                            <input type="text" name="company_name" id="company_name" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="city" class="mb-2 block text-sm font-medium text-slate-700">City</label>
                                <input type="text" name="city" id="city" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="country" class="mb-2 block text-sm font-medium text-slate-700">Country</label>
                                <input type="text" name="country" id="country" value="Pakistan" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                            <select name="status" id="status" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        @if ($errors->any())
                            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                            Save Vendor
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
