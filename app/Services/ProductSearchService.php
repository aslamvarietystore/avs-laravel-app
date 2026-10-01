<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductSearchService
{
    public function search(string $query): Collection
    {
        $term = trim($query);

        if ($term === '') {
            return collect();
        }

        $normalized = strtolower($term);

        return Product::query()
            ->with(['category', 'productImages'])
            ->where(function ($builder) use ($normalized) {
                $builder->whereRaw('LOWER(barcode) = ?', [$normalized])
                    ->orWhereRaw('LOWER(item_number) = ?', [$normalized])
                    ->orWhereRaw('LOWER(sku) = ?', [$normalized])
                    ->orWhereRaw('LOWER(barcode) LIKE ?', [$normalized.'%'])
                    ->orWhereRaw('LOWER(item_number) LIKE ?', [$normalized.'%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%'.$normalized.'%']);
            })
            ->orderByRaw(
                'CASE
                    WHEN LOWER(barcode) = ? THEN 1
                    WHEN LOWER(item_number) = ? THEN 2
                    WHEN LOWER(sku) = ? THEN 3
                    WHEN LOWER(barcode) LIKE ? THEN 4
                    WHEN LOWER(item_number) LIKE ? THEN 5
                    WHEN LOWER(name) LIKE ? THEN 6
                    ELSE 7
                END',
                [$normalized, $normalized, $normalized, $normalized.'%', $normalized.'%', '%'.$normalized.'%']
            )
            ->limit(20)
            ->get();
    }
}
