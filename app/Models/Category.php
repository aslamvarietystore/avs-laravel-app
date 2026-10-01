<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'status',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function subcategoryProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'subcategory_id');
    }

    public function isSubcategory(): bool
    {
        return $this->parent_id !== null;
    }

    /**
     * Top-level categories that can own sub-categories.
     *
     * @param  Builder<Category>  $query
     */
    public function scopeParents(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    /**
     * Sub-categories that always belong to a top-level category.
     *
     * @param  Builder<Category>  $query
     */
    public function scopeSubcategories(Builder $query): void
    {
        $query->whereNotNull('parent_id');
    }
}
