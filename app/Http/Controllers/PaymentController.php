<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::query()->with('vendor')->latest('paid_on')->get();
        $vendors = Supplier::query()->orderBy('name')->get();

        return view('payments.index', compact('payments', 'vendors'));
    }

    public function create(): View
    {
        return view('payments.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => ['required', 'exists:suppliers,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', 'in:cash,bank,easypaisa,jazzcash'],
            'paid_on' => ['required', 'date'],
            'status' => ['required', 'string', 'in:paid,pending,overdue'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Payment::query()->create($validated);

        return redirect()->route('payments.index')->with('status', 'Payment saved successfully.');
    }
}
