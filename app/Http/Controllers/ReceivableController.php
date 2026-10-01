<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Receivable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceivableController extends Controller
{
    public function index(): View
    {
        $receivables = Receivable::query()->with('customer')->latest('due_date')->get();

        return view('receivables.index', compact('receivables'));
    }

    public function create(): View
    {
        $customers = Customer::query()->orderBy('name')->get();

        return view('receivables.index', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'invoice_number' => ['required', 'string', 'max:50', 'unique:receivables,invoice_number'],
            'amount_due' => ['required', 'numeric', 'min:0.01'],
            'amount_received' => ['required', 'numeric', 'min:0'],
            'balance' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:paid,partial,overdue'],
            'due_date' => ['required', 'date'],
        ]);

        Receivable::query()->create($validated);

        return redirect()->route('receivables.index')->with('status', 'Receivable saved successfully.');
    }
}
