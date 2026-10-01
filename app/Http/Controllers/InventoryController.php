<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class InventoryController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category')
            ->orderBy('stock_quantity')
            ->get();

        $totalItems = $products->sum('stock_quantity');
        $lowStockItems = $products->filter(fn (Product $product) => $product->stock_quantity <= $product->low_stock_threshold)->count();

        return view('inventory.index', compact('products', 'totalItems', 'lowStockItems'));
    }

    public function adjust(Request $request, InventoryService $inventoryService): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'string', 'in:purchase,sale,adjustment'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
        $adjustment = match ($validated['type']) {
            'sale' => -abs((int) $validated['quantity']),
            'adjustment' => (int) $validated['quantity'] * (($request->input('direction', 'increase') === 'decrease') ? -1 : 1),
            default => abs((int) $validated['quantity']),
        };

        try {
            $inventoryService->adjustStock($product, $adjustment, $validated['type'], $validated['notes'] ?? null, 'inventory', $product->id);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['stock' => $exception->getMessage()])->withInput();
        }

        return redirect()->route('inventory.index')->with('status', 'Stock updated successfully.');
    }

    public function packaging(): View
    {
        $products = Product::query()
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('inventory.packaging', compact('products'));
    }

    public function updatePackSizes(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'pieces_per_packet' => ['required', 'integer', 'min:0'],
            'pieces_per_box' => ['required', 'integer', 'min:0'],
            'pieces_per_carton' => ['required', 'integer', 'min:0'],
        ]);

        Product::query()->whereKey($validated['product_id'])->update([
            'pieces_per_packet' => $validated['pieces_per_packet'],
            'pieces_per_box' => $validated['pieces_per_box'],
            'pieces_per_carton' => $validated['pieces_per_carton'],
        ]);

        return redirect()->route('inventory.packaging')->with('status', 'Pack sizes updated successfully.');
    }
}
