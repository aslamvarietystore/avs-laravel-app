<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SlugService
{
    public function unique(string $value, string $table, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $base = $base === '' ? 'item' : $base;
        $slug = $base;
        $suffix = 2;

        while ($this->slugExists($table, $slug, $ignoreId)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function slugExists(string $table, string $slug, ?int $ignoreId): bool
    {
        return DB::table($table)
            ->where('slug', $slug)
            ->when($ignoreId !== null, function ($query) use ($ignoreId) {
                return $query->where('id', '!=', $ignoreId);
            })
            ->exists();
    }
}
