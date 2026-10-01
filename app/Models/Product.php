<?php

namespace App\Models;

use App\Services\PricingService;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'brand_id',
        'item_number',
        'barcode',
        'sku',
        'name',
        'slug',
        'description',
        'china_price',
        'exchange_rate',
        'cost_pkr',
        'wholesale_price',
        'retail_price',
        'minimum_order_quantity',
        'stock_quantity',
        'low_stock_threshold',
        'unit',
        'pieces_per_packet',
        'pieces_per_box',
        'pieces_per_carton',
        'status',
        'is_featured',
        'is_new_arrival',
        'is_sale',
    ];

    protected $casts = [
        'china_price' => 'decimal:2',
        'exchange_rate' => 'decimal:2',
        'cost_pkr' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'minimum_order_quantity' => 'integer',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'pieces_per_packet' => 'integer',
        'pieces_per_box' => 'integer',
        'pieces_per_carton' => 'integer',
        'is_featured' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_sale' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categoryPath(): string
    {
        return collect([$this->category?->name, $this->subcategory?->name])
            ->filter()
            ->implode(' › ') ?: 'Uncategorised';
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function primaryImage(): ?ProductImage
    {
        return $this->productImages()->where('is_primary', true)->first()
            ?? $this->productImages()->first();
    }

    public function calculateCostPkr(?float $exchangeRate = null): float
    {
        $rate = $exchangeRate ?? app(PricingService::class)->getCurrentExchangeRate();

        return round((float) ($this->china_price * $rate), 2);
    }

    public function piecesPerSaleUnit(string $unit): ?int
    {
        return match ($unit) {
            'pcs' => 1,
            'dozen' => 12,
            'packet' => $this->pieces_per_packet > 0 ? $this->pieces_per_packet : null,
            'box' => $this->pieces_per_box > 0 ? $this->pieces_per_box : null,
            'carton' => $this->pieces_per_carton > 0 ? $this->pieces_per_carton : null,
            default => null,
        };
    }

    /** @return array<string, int> */
    public function availableSaleUnits(): array
    {
        return array_filter([
            'pcs' => $this->piecesPerSaleUnit('pcs'),
            'dozen' => $this->piecesPerSaleUnit('dozen'),
            'packet' => $this->piecesPerSaleUnit('packet'),
            'box' => $this->piecesPerSaleUnit('box'),
            'carton' => $this->piecesPerSaleUnit('carton'),
        ]);
    }
}
