<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Receivable;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function createOrder(Customer $customer, array $items, array $attributes = []): Order
    {
        return DB::transaction(function () use ($customer, $items, $attributes) {
            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer->id,
                'shipping_address_id' => $attributes['shipping_address_id'] ?? null,
                'subtotal' => 0,
                'discount' => $attributes['discount'] ?? 0,
                'shipping_amount' => $attributes['shipping_amount'] ?? 0,
                'total' => 0,
                'currency' => $attributes['currency'] ?? 'PKR',
                'payment_status' => $attributes['payment_status'] ?? 'unpaid',
                'payment_method' => $attributes['payment_method'] ?? 'cash',
                'amount_paid' => 0,
                'order_status' => $attributes['order_status'] ?? 'pending',
                'notes' => $attributes['notes'] ?? null,
                'created_by' => $attributes['created_by'] ?? null,
            ]);

            $subtotal = 0;

            foreach ($items as $item) {
                $product = Product::query()->findOrFail($item['product_id']);
                $quantity = (int) ($item['quantity'] ?? 0);
                $unit = (string) ($item['unit'] ?? 'pcs');
                $piecesPerUnit = $product->piecesPerSaleUnit($unit);
                $unitPrice = (float) ($item['unit_price'] ?? $product->wholesale_price ?? 0);

                if ($quantity <= 0 || $piecesPerUnit === null) {
                    throw new RuntimeException('Order quantity must be greater than zero.');
                }

                $baseQuantity = $quantity * $piecesPerUnit;

                if ($product->stock_quantity < $baseQuantity) {
                    throw new RuntimeException('Not enough stock for '.$product->name.'.');
                }

                $this->inventoryService->removeStock(
                    $product,
                    $baseQuantity,
                    'Order '.$order->order_number,
                    Order::class,
                    $order->id
                );

                $lineSubtotal = $quantity * $unitPrice;
                $subtotal += $lineSubtotal;

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'item_number' => $product->item_number,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit' => $unit,
                    'base_quantity' => $baseQuantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                ]);
            }

            $discount = (float) ($attributes['discount'] ?? 0);
            $shippingAmount = (float) ($attributes['shipping_amount'] ?? 0);
            $total = $subtotal - $discount + $shippingAmount;
            $paymentMethod = $attributes['payment_method'] ?? 'cash';
            $amountPaid = (float) ($attributes['amount_paid'] ?? ($paymentMethod === 'cash' ? $total : 0));

            if (! in_array($paymentMethod, ['cash', 'credit'], true) || $amountPaid < 0 || $amountPaid > $total) {
                throw new RuntimeException('Payment details are invalid.');
            }

            if ($paymentMethod === 'cash' && $amountPaid < $total) {
                throw new RuntimeException('Cash orders must be paid in full. Choose credit for an outstanding balance.');
            }

            $order->subtotal = $subtotal;
            $order->discount = $discount;
            $order->shipping_amount = $shippingAmount;
            $order->total = $total;
            $order->payment_method = $paymentMethod;
            $order->amount_paid = $amountPaid;
            $order->payment_status = $amountPaid >= $total ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid');
            $order->save();

            if ($amountPaid < $total) {
                Receivable::query()->create([
                    'customer_id' => $customer->id,
                    'invoice_number' => $order->order_number,
                    'amount_due' => $total,
                    'amount_received' => $amountPaid,
                    'balance' => $total - $amountPaid,
                    'status' => $amountPaid > 0 ? 'partial' : 'overdue',
                    'due_date' => $attributes['due_date'] ?? now()->addDays(30)->toDateString(),
                ]);
            }

            return $order->fresh('items');
        });
    }

    protected function generateOrderNumber(): string
    {
        $count = Order::query()->count() + 1;

        return 'AVS-'.date('Ymd').'-'.str_pad((string) $count, 5, '0', STR_PAD_LEFT);
    }
}
