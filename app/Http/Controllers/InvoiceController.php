<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::query()->with('customer')->latest('issued_on')->get();

        return view('invoices.index', compact('invoices'));
    }

    public function create(): View
    {
        $customers = Customer::query()->orderBy('name')->get();

        return view('invoices.index', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'invoice_number' => ['required', 'string', 'max:50', 'unique:invoices,invoice_number'],
            'issued_on' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issued_on'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'string', 'in:paid,pending,overdue'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        Invoice::query()->create($validated);

        return redirect()->route('invoices.index')->with('status', 'Invoice saved successfully.');
    }
}
