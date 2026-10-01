<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Aslam Variety Store</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-800 antialiased">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.35em] text-indigo-600">AVS</p>
                    <h1 class="text-xl font-black text-slate-900 sm:text-2xl">Aslam Variety Store</h1>
                </div>
                <nav class="hidden items-center gap-6 text-sm font-semibold text-slate-600 sm:flex">
                    <a href="{{ route('storefront.home') }}" class="transition hover:text-indigo-600">Home</a>
                    <a href="{{ route('storefront.products') }}" class="transition hover:text-indigo-600">Products</a>
                    <a href="{{ route('products.search') }}" class="transition hover:text-indigo-600">Search</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-900 px-4 py-2 text-white hover:bg-slate-700">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-slate-300 px-4 py-2 hover:border-slate-400">Log in</a>
                        @endauth
                    @endif
                </nav>
                <a href="{{ route('storefront.products') }}" class="inline-flex rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:hidden">Shop</a>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            <section class="overflow-hidden rounded-[28px] bg-gradient-to-r from-indigo-700 via-purple-700 to-sky-600 p-6 text-white shadow-2xl shadow-indigo-900/20 sm:p-8 lg:p-10">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-indigo-100">Wholesale & retail</p>
                        <h2 class="mt-4 text-3xl font-black leading-tight sm:text-4xl lg:text-5xl">Smart essentials for your everyday shop</h2>
                        <p class="mt-4 max-w-xl text-sm text-indigo-100 sm:text-base">Discover trusted products, check live stock, and compare pricing with a store experience built for speed and convenience.</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('storefront.products') }}" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-indigo-700 shadow-lg hover:bg-indigo-50">Shop now</a>
                        <a href="{{ route('products.search') }}" class="inline-flex items-center justify-center rounded-full border border-white/40 px-6 py-3 text-sm font-bold text-white hover:bg-white/10">Quick search</a>
                    </div>
                </div>
            </section>

            <section class="mt-10">
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Top picks</p>
                        <h3 class="mt-1 text-2xl font-black text-slate-900">Featured products</h3>
                    </div>
                    <a href="{{ route('storefront.products') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-500">Browse all</a>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($products->take(6) as $product)
                        @php
                            $primaryImage = $product->primaryImage();
                            $imageUrl = $primaryImage ? asset($primaryImage->image_path) : null;
                        @endphp
                        <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-xl">
                            <div class="relative flex h-56 items-center justify-center overflow-hidden bg-gradient-to-br from-slate-100 via-white to-indigo-50">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                @else
                                    <div class="flex flex-col items-center gap-2 text-slate-500">
                                        <span class="text-5xl">📦</span>
                                        <span class="text-xs font-semibold uppercase tracking-[0.25em]">Product</span>
                                    </div>
                                @endif
                                <span class="absolute left-4 top-4 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-700 shadow-sm">{{ $product->category?->name ?? 'General' }}</span>
                            </div>
                            <div class="space-y-4 p-4 sm:p-5">
                                <div>
                                    <div class="flex items-center justify-between gap-3">
                                        <h4 class="truncate text-lg font-black text-slate-900">{{ $product->name }}</h4>
                                        <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-700">{{ $product->stock_quantity > 0 ? 'In stock' : 'Low stock' }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">{{ $product->item_number }} · {{ $product->stock_quantity }} available</p>
                                </div>
                                <div class="flex items-end justify-between gap-2">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-slate-500">Wholesale Price</p>
                                        <p class="mt-1 text-xl font-black text-emerald-600">Rs. {{ number_format($product->wholesale_price, 2) }}</p>
                                    </div>
                                    <a href="{{ route('products.show', $product) }}" class="inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">View</a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-slate-600 sm:col-span-2 xl:col-span-3">
                            No active products are available right now.
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </body>
</html>
