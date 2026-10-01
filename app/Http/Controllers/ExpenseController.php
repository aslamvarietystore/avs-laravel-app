<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::query()->with('vendor')->latest('incurred_on')->get();
        $vendors = Supplier::query()->orderBy('name')->get();

        return view('expenses.index', compact('expenses', 'vendors'));
    }

    public function create(): View
    {
        return view('expenses.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => ['nullable', 'exists:suppliers,id'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'incurred_on' => ['required', 'date'],
            'status' => ['required', 'string', 'in:paid,pending,overdue'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Expense::query()->create($validated);

        return redirect()->route('expenses.index')->with('status', 'Expense saved successfully.');
    }
}
