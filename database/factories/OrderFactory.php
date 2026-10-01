<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'AVS-'.date('Ymd').'-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
            'customer_id' => Customer::factory(),
            'shipping_address_id' => null,
            'subtotal' => 390,
            'discount' => 0,
            'shipping_amount' => 0,
            'total' => 390,
            'currency' => 'PKR',
            'payment_status' => 'paid',
            'order_status' => 'pending',
            'notes' => 'Sample order',
            'created_by' => null,
        ];
    }
}
