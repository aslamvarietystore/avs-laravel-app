<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Quotation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(): View
    {
        $quotations = Quotation::query()->with('customer')->latest('issued_on')->get();

        return view('quotations.index', compact('quotations'));
    }

    public function create(): View
    {
        $customers = Customer::query()->orderBy('name')->get();

        return view('quotations.index', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'quotation_number' => ['required', 'string', 'max:50', 'unique:quotations,quotation_number'],
            'issued_on' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:issued_on'],
            'subtotal' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'string', 'in:approved,pending,rejected'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        Quotation::query()->create($validated);

        return redirect()->route('quotations.index')->with('status', 'Quotation saved successfully.');
    }
}
