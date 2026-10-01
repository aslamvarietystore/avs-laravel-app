<?php

use App\Models\PayrollEntry;
use App\Models\User;

it('shows the payroll screen with recorded salary entries', function () {
    $user = User::factory()->create([
        'name' => 'Ali Khan',
        'role' => User::ROLE_STAFF,
    ]);

    PayrollEntry::query()->create([
        'user_id' => $user->id,
        'period' => '2026-09',
        'basic_salary' => 60000,
        'allowances' => 8000,
        'deductions' => 3000,
        'net_salary' => 65000,
        'status' => 'paid',
        'paid_on' => now()->toDateString(),
    ]);

    $response = $this->actingAs($user)->get(route('payroll.index'));

    $response->assertOk()
        ->assertSee('Staff Payroll')
        ->assertSee('Ali Khan');
});

it('allows staff to create a payroll entry', function () {
    $user = User::factory()->create([
        'name' => 'Sara Ahmed',
        'role' => User::ROLE_STAFF,
    ]);

    $response = $this->actingAs($user)->post(route('payroll.store'), [
        'user_id' => $user->id,
        'period' => '2026-09',
        'basic_salary' => 70000,
        'allowances' => 10000,
        'deductions' => 4000,
        'net_salary' => 77000,
        'status' => 'paid',
        'paid_on' => now()->toDateString(),
    ]);

    $response->assertRedirect(route('payroll.index'))
        ->assertSessionHas('status', 'Payroll entry saved successfully.');

    expect(PayrollEntry::query()->where('user_id', $user->id)->count())->toBe(1);
});
