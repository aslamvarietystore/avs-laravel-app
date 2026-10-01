<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->where('status', 'active')
            ->orderByDesc('is_featured')
            ->latest()
            ->get();

        $categories = Category::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('storefront.index', compact('products', 'categories'));
    }

    public function catalog(Request $request): View
    {
        $query = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category');

        $products = Product::query()
            ->where('status', 'active')
            ->when($categoryId, fn ($queryBuilder) => $queryBuilder->where('category_id', $categoryId))
            ->when($query !== '', fn ($queryBuilder) => $queryBuilder->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('item_number', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            }))
            ->orderByDesc('is_featured')
            ->latest()
            ->get();

        $categories = Category::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('storefront.catalog', compact('products', 'categories', 'query', 'categoryId'));
    }
}
