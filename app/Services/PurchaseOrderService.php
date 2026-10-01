<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PurchaseOrderService
{
    public function __construct(private readonly InventoryService $inventoryService) {}

    public function createPurchaseOrder(
        Supplier $supplier,
        Product $product,
        int $quantity,
        float $unitCost,
        ?string $notes = null,
        ?int $createdBy = null,
        string $unit = 'pcs',
    ): PurchaseOrder {
        $piecesPerUnit = $product->piecesPerSaleUnit($unit);

        if ($piecesPerUnit === null) {
            throw new InvalidArgumentException('The selected purchase unit is not configured for this product.');
        }

        $baseQuantity = $quantity * $piecesPerUnit;
        $subtotal = round($quantity * $unitCost, 2);

        return DB::transaction(function () use ($supplier, $product, $quantity, $unitCost, $subtotal, $baseQuantity, $unit, $notes, $createdBy) {
            $purchaseOrder = PurchaseOrder::query()->create([
                'purchase_number' => 'PO-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
                'supplier_id' => $supplier->id,
                'subtotal' => $subtotal,
                'shipping_amount' => 0,
                'total' => $subtotal,
                'status' => 'received',
                'notes' => $notes,
                'created_by' => $createdBy,
            ]);

            $purchaseOrder->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit' => $unit,
                'base_quantity' => $baseQuantity,
                'unit_cost' => $unitCost,
                'subtotal' => $subtotal,
            ]);

            $this->inventoryService->addStock(
                $product,
                $baseQuantity,
                $notes ?? 'Purchase order '.$purchaseOrder->purchase_number,
                'purchase_order',
                $purchaseOrder->id,
            );

            return $purchaseOrder->fresh(['supplier', 'items.product']);
        });
    }
}
