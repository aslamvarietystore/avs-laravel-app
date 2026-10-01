<?php

namespace App\Http\Controllers;

use App\Models\PayrollEntry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(): View
    {
        $entries = PayrollEntry::query()->with('user')->latest('paid_on')->get();
        $staff = User::query()->whereIn('role', [User::ROLE_STAFF, User::ROLE_ADMIN])->get();

        return view('payroll.index', compact('entries', 'staff'));
    }

    public function create(): View
    {
        $staff = User::query()->whereIn('role', [User::ROLE_STAFF, User::ROLE_ADMIN])->get();

        return view('payroll.index', compact('staff'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'period' => ['required', 'string', 'max:20'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'allowances' => ['required', 'numeric', 'min:0'],
            'deductions' => ['required', 'numeric', 'min:0'],
            'net_salary' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:paid,pending'],
            'paid_on' => ['nullable', 'date'],
        ]);

        PayrollEntry::query()->create($validated);

        return redirect()->route('payroll.index')->with('status', 'Payroll entry saved successfully.');
    }
}
