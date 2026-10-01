<?php

namespace App\Http\Controllers;

use App\Models\CashflowEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashbookController extends Controller
{
    public function index(): View
    {
        $entries = CashflowEntry::query()->latest('entry_date')->get();
        $balance = $entries->sum(fn ($entry) => $entry->type === 'in' ? $entry->amount : -$entry->amount);

        return view('cashbook.index', compact('entries', 'balance'));
    }

    public function create(): View
    {
        return view('cashbook.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:in,out'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        CashflowEntry::query()->create($validated);

        return redirect()->route('cashbook.index')->with('status', 'Cashbook entry saved successfully.');
    }
}
