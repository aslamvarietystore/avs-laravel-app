<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayableController extends Controller
{
    public function index(): View
    {
        $payables = Payable::query()->with('supplier')->latest('due_date')->get();

        return view('payables.index', compact('payables'));
    }

    public function create(): View
    {
        $suppliers = Supplier::query()->orderBy('name')->get();

        return view('payables.index', compact('suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'invoice_number' => ['required', 'string', 'max:50', 'unique:payables,invoice_number'],
            'amount_due' => ['required', 'numeric', 'min:0.01'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'balance' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:paid,partial,overdue'],
            'due_date' => ['required', 'date'],
        ]);

        Payable::query()->create($validated);

        return redirect()->route('payables.index')->with('status', 'Payable saved successfully.');
    }
}
