<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Staff Payroll
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
                        <h1 class="text-2xl font-bold text-slate-900">Staff Payroll</h1>
                        <p class="mt-1 text-sm text-slate-500">Track staff wages, allowances, deductions, and payment status.</p>
                    </div>
                    <a href="{{ route('payroll.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        New Payroll Entry
                    </a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.35fr_0.65fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Payroll Records</h2>
                    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Staff</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Period</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Net</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse ($entries as $entry)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $entry->user?->name ?? 'Unknown Staff' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $entry->period }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-900">Rs. {{ number_format($entry->net_salary, 2) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $entry->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                {{ $entry->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No payroll entries yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Add Payroll Entry</h2>
                    <form method="POST" action="{{ route('payroll.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="user_id" class="mb-2 block text-sm font-medium text-slate-700">Employee</label>
                            <select name="user_id" id="user_id" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                                <option value="">Select staff</option>
                                @foreach ($staff as $staffMember)
                                    <option value="{{ $staffMember->id }}">{{ $staffMember->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="period" class="mb-2 block text-sm font-medium text-slate-700">Period</label>
                            <input type="text" name="period" id="period" value="{{ now()->format('Y-m') }}" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="basic_salary" class="mb-2 block text-sm font-medium text-slate-700">Basic Salary</label>
                                <input type="number" step="0.01" min="0" name="basic_salary" id="basic_salary" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                            </div>
                            <div>
                                <label for="allowances" class="mb-2 block text-sm font-medium text-slate-700">Allowances</label>
                                <input type="number" step="0.01" min="0" name="allowances" id="allowances" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="deductions" class="mb-2 block text-sm font-medium text-slate-700">Deductions</label>
                                <input type="number" step="0.01" min="0" name="deductions" id="deductions" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                            </div>
                            <div>
                                <label for="net_salary" class="mb-2 block text-sm font-medium text-slate-700">Net Salary</label>
                                <input type="number" step="0.01" min="0" name="net_salary" id="net_salary" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                                <select name="status" id="status" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" required>
                                    <option value="paid">Paid</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div>
                                <label for="paid_on" class="mb-2 block text-sm font-medium text-slate-700">Paid On</label>
                                <input type="date" name="paid_on" id="paid_on" value="{{ now()->toDateString() }}" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                            Save Payroll Entry
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
