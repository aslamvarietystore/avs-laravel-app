<?php

namespace App\Services;

use App\Models\Product;

class ItemNumberService
{
    public const PREFIX = 'TJ2-';

    public function nextNumber(): string
    {
        $sequence = Product::withTrashed()->count() + 1;

        do {
            $candidate = self::PREFIX.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Product::withTrashed()->where('item_number', $candidate)->exists());

        return $candidate;
    }
}
