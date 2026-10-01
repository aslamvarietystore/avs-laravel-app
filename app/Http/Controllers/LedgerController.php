<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function index(): View
    {
        $entries = LedgerEntry::query()->latest('entry_date')->get();
        $balance = $entries->sum(fn ($entry) => $entry->type === 'credit' ? $entry->amount : -$entry->amount);

        return view('ledger.index', compact('entries', 'balance'));
    }

    public function create(): View
    {
        return view('ledger.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        LedgerEntry::query()->create($validated);

        return redirect()->route('ledger.index')->with('status', 'Ledger entry saved successfully.');
    }
}
