<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function addStock(Product $product, int $quantity, ?string $notes = null, ?string $referenceType = null, ?int $referenceId = null): StockMovement
    {
        return $this->adjustStock($product, abs($quantity), 'purchase', $notes, $referenceType, $referenceId);
    }

    public function removeStock(Product $product, int $quantity, ?string $notes = null, ?string $referenceType = null, ?int $referenceId = null): StockMovement
    {
        return $this->adjustStock($product, -abs($quantity), 'sale', $notes, $referenceType, $referenceId);
    }

    public function adjustStock(Product $product, int $quantity, string $type = 'adjustment', ?string $notes = null, ?string $referenceType = null, ?int $referenceId = null): StockMovement
    {
        if ($quantity === 0) {
            return StockMovement::query()->create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => 0,
                'previous_quantity' => $product->stock_quantity,
                'new_quantity' => $product->stock_quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        }

        return DB::transaction(function () use ($product, $quantity, $type, $notes, $referenceType, $referenceId) {
            $previousQuantity = (int) $product->stock_quantity;
            $newQuantity = $previousQuantity + $quantity;

            if ($newQuantity < 0) {
                throw new RuntimeException('Stock cannot become negative.');
            }

            $product->stock_quantity = $newQuantity;
            $product->save();

            return StockMovement::query()->create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $quantity,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }
}
