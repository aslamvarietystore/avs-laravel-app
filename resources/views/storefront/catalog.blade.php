<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Shop Products | Aslam Variety Store</title>
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
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-900 px-4 py-2 text-white hover:bg-slate-700">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-slate-300 px-4 py-2 hover:border-slate-400">Log in</a>
                        @endauth
                    @endif
                </nav>
                <a href="{{ route('storefront.home') }}" class="inline-flex rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:hidden">Home</a>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Browse</p>
                        <h2 class="mt-2 text-3xl font-black text-slate-900">All products</h2>
                    </div>

                    <form method="GET" action="{{ route('storefront.products') }}" class="grid w-full max-w-2xl gap-3 sm:grid-cols-[1.3fr_0.8fr_auto]">
                        <input type="text" name="search" value="{{ $query }}" placeholder="Search product or barcode" class="w-full rounded-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white" />
                        <select name="category" class="rounded-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) $categoryId === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-full bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-500">Search</button>
                    </form>
                </div>
            </section>

            <section class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
                @forelse ($products as $product)
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
                                    <span class="text-5xl">🛍️</span>
                                    <span class="text-xs font-semibold uppercase tracking-[0.25em]">Product</span>
                                </div>
                            @endif
                            <span class="absolute left-4 top-4 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-700 shadow-sm">{{ $product->category?->name ?? 'General' }}</span>
                        </div>
                        <div class="space-y-4 p-4 sm:p-5">
                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="truncate text-lg font-black text-slate-900">{{ $product->name }}</h3>
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
                        No products match your current filters.
                    </div>
                @endforelse
            </section>
        </main>
    </body>
</html>
