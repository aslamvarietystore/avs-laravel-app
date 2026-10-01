<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function __construct(private readonly PurchaseOrderService $purchaseOrderService) {}

    public function index(): View
    {
        $purchaseOrders = PurchaseOrder::query()
            ->with(['supplier', 'items.product'])
            ->latest()
            ->get();

        $suppliers = Supplier::query()->orderBy('name')->get();

        return view('purchase-orders.index', compact('purchaseOrders', 'suppliers'));
    }

    public function create(): View
    {
        $suppliers = Supplier::query()->orderBy('name')->get();
        $products = Product::query()->where('status', 'active')->orderBy('name')->get();

        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['sometimes', 'string', 'in:pcs,dozen,packet,box,carton'],
            'unit_cost' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $supplier = Supplier::query()->findOrFail($validated['supplier_id']);
        $product = Product::query()->findOrFail($validated['product_id']);

        $this->purchaseOrderService->createPurchaseOrder(
            $supplier,
            $product,
            (int) $validated['quantity'],
            (float) $validated['unit_cost'],
            $validated['notes'] ?? null,
            Auth::id(),
            $validated['unit'] ?? 'pcs',
        );

        return redirect()->route('purchase-orders.index')->with('status', 'Purchase order created successfully.');
    }
}
