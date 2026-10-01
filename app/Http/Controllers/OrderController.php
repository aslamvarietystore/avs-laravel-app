<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    public function create(): View
    {
        $customers = Customer::query()->orderBy('name')->get();
        $products = Product::query()->where('status', 'active')->orderBy('name')->get();

        return view('orders.create', compact('customers', 'products'));
    }

    public function store(Request $request, OrderService $orderService): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['required', 'string', 'in:pcs,dozen,packet,box,carton'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:cash,credit'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $customer = Customer::query()->findOrFail($validated['customer_id']);
        $product = Product::query()->findOrFail($validated['product_id']);

        if ($product->piecesPerSaleUnit($validated['unit']) === null) {
            return back()->withErrors(['unit' => 'Configure this product\'s pack size before selling it by that unit.'])->withInput();
        }

        try {
            $orderService->createOrder($customer, [[
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'unit_price' => $validated['unit_price'],
            ]], [
                'notes' => $validated['notes'] ?? null,
                'payment_method' => $validated['payment_method'],
                'amount_paid' => $validated['amount_paid'] ?? null,
                'order_status' => 'completed',
            ]);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['order' => $exception->getMessage()])->withInput();
        }

        return redirect()->route('orders.create')->with('status', 'Order created successfully.');
    }
}
