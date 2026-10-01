<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 font-sans text-slate-800 antialiased">
        <div x-data="{ open: false }" class="min-h-screen">
            <aside :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-slate-200 bg-slate-950 text-slate-100 transition-transform duration-200 lg:translate-x-0">
                <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-800 px-6">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-indigo-300">AVS</p>
                        <h1 class="text-lg font-bold text-white">Aslam Variety</h1>
                    </div>
                    <button @click="open = false" class="rounded-full border border-slate-700 p-2 text-slate-300 lg:hidden">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                @php
                    $menuSections = [
                        [
                            'label' => null,
                            'items' => [
                                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => '⌂'],
                            ],
                        ],
                        [
                            'label' => 'Catalog',
                            'items' => [
                                ['label' => 'Product Search', 'route' => 'products.search', 'icon' => '⌕'],
                                ['label' => 'Items', 'route' => 'products.index', 'icon' => '◫', 'match' => ['products.index', 'products.create', 'products.edit']],
                                ['label' => 'Categories', 'route' => 'categories.index', 'icon' => '⌗', 'match' => ['categories.index', 'categories.create', 'categories.edit']],
                                ['label' => 'Packaging', 'route' => 'inventory.packaging', 'icon' => '◨'],
                                ['label' => 'Inventory', 'route' => 'inventory.index', 'icon' => '▣'],
                            ],
                        ],
                        [
                            'label' => 'Sales',
                            'items' => [
                                ['label' => 'Customers', 'route' => 'customers.index', 'icon' => '◉'],
                                ['label' => 'Orders', 'route' => 'orders.create', 'icon' => '⟐'],
                                ['label' => 'Invoices', 'route' => 'invoices.index', 'icon' => '◔'],
                                ['label' => 'Quotations', 'route' => 'quotations.index', 'icon' => '✦'],
                                ['label' => 'Returns', 'route' => 'returns.index', 'icon' => '↩'],
                                ['label' => 'Receivables', 'route' => 'receivables.index', 'icon' => '◒'],
                            ],
                        ],
                        [
                            'label' => 'Purchasing',
                            'items' => [
                                ['label' => 'Vendors', 'route' => 'vendors.index', 'icon' => '◌'],
                                ['label' => 'Purchase Orders', 'route' => 'purchase-orders.index', 'icon' => '◍'],
                                ['label' => 'Payables', 'route' => 'payables.index', 'icon' => '◓'],
                                ['label' => 'Expenses', 'route' => 'expenses.index', 'icon' => '⎔'],
                                ['label' => 'Payments', 'route' => 'payments.index', 'icon' => '◐'],
                            ],
                        ],
                        [
                            'label' => 'Accounts',
                            'items' => [
                                ['label' => 'Cashbook', 'route' => 'cashbook.index', 'icon' => '◖'],
                                ['label' => 'Ledger', 'route' => 'ledger.index', 'icon' => '◗'],
                                ['label' => 'Payroll', 'route' => 'payroll.index', 'icon' => '◘'],
                            ],
                        ],
                        [
                            'label' => 'Settings',
                            'items' => [
                                ['label' => 'Currency Rates', 'route' => 'exchange-rates.index', 'icon' => '⇄', 'match' => ['exchange-rates.index', 'exchange-rates.create']],
                                ['label' => 'Storefront', 'route' => 'storefront.home', 'icon' => '⬢'],
                            ],
                        ],
                    ];

                    $linkClass = 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition';
                    $activeClass = 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20';
                    $idleClass = 'text-slate-300 hover:bg-slate-800 hover:text-white';
                @endphp

                <nav class="flex-1 space-y-5 overflow-y-auto px-4 py-5">
                    @foreach ($menuSections as $section)
                        <div>
                            @if ($section['label'])
                                <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.3em] text-slate-500">{{ $section['label'] }}</p>
                            @endif

                            <div class="space-y-1">
                                @foreach ($section['items'] as $item)
                                    <a href="{{ route($item['route']) }}" class="{{ $linkClass }} {{ request()->routeIs(...($item['match'] ?? [$item['route']])) ? $activeClass : $idleClass }}">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/5 text-xs">{{ $item['icon'] }}</span>
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>

                <div class="shrink-0 border-t border-slate-800 px-6 py-4">
                    <p class="text-[10px] uppercase tracking-[0.25em] text-slate-500">Signed in as</p>
                    <p class="mt-1 truncate text-sm font-semibold text-slate-200">{{ auth()->user()?->name ?? 'Guest' }}</p>
                </div>
            </aside>

            <div class="lg:ml-72">
                <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur-xl">
                    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button @click="open = ! open" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-700 lg:hidden">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            </button>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-indigo-600">AVS</p>
                                <h2 class="text-lg font-semibold text-slate-900">Operations Center</h2>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('storefront.products') }}" class="hidden rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 sm:inline-flex">Shop</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Logout</button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    @isset($header)
                        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            {{ $header }}
                        </div>
                    @endisset

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
