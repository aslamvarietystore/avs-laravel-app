<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ReturnRecord;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService) {}

    public function index(): View
    {
        $returns = ReturnRecord::query()->with('product')->latest('returned_on')->get();
        $products = Product::query()->where('status', 'active')->orderBy('name')->get();

        return view('returns.index', compact('returns', 'products'));
    }

    public function create(): View
    {
        return view('returns.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'amount_refunded' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
            'returned_on' => ['required', 'date'],
            'status' => ['required', 'string', 'in:approved,pending,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);

        ReturnRecord::query()->create($validated);

        $this->inventoryService->addStock(
            $product,
            (int) $validated['quantity'],
            'Return recorded: '.$validated['reason'],
            'return',
            null,
        );

        return redirect()->route('returns.index')->with('status', 'Return recorded successfully.');
    }
}
