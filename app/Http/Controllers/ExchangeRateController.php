<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExchangeRateController extends Controller
{
    public function __construct(
        protected ExchangeRateService $exchangeRateService,
    ) {}

    public function index(): View
    {
        return view('exchange-rates.index', $this->viewData());
    }

    public function create(): View
    {
        return $this->index();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'currency_from' => ['required', 'string', 'max:10'],
            'currency_to' => ['required', 'string', 'max:10'],
            'rate' => ['required', 'numeric', 'min:0.01'],
            'effective_date' => ['required', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->exchangeRateService->record(
            $validated['currency_from'],
            $validated['currency_to'],
            (float) $validated['rate'],
            $validated['effective_date'],
            $request->boolean('is_active'),
            $validated['notes'] ?? null,
            Auth::id(),
        );

        return redirect()
            ->route('exchange-rates.index')
            ->with('status', 'Exchange rate saved successfully.');
    }

    public function activate(ExchangeRate $exchangeRate): RedirectResponse
    {
        $this->exchangeRateService->activate($exchangeRate);

        return redirect()
            ->route('exchange-rates.index')
            ->with('status', 'Exchange rate activated successfully.');
    }

    public function destroy(ExchangeRate $exchangeRate): RedirectResponse
    {
        if ($exchangeRate->is_active) {
            return redirect()
                ->route('exchange-rates.index')
                ->withErrors(['rate' => 'Activate another rate before removing the active rate.']);
        }

        $exchangeRate->delete();

        return redirect()
            ->route('exchange-rates.index')
            ->with('status', 'Exchange rate removed successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function viewData(): array
    {
        $activeRate = $this->exchangeRateService->currentRate();

        return [
            'exchangeRates' => ExchangeRate::query()
                ->orderByDesc('effective_date')
                ->orderByDesc('id')
                ->get(),
            'activeRate' => $activeRate,
        ];
    }
}
