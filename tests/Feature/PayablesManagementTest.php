<?php

use App\Models\Payable;
use App\Models\Supplier;
use App\Models\User;

it('shows the payables screen with outstanding supplier balances', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create([
        'name' => 'Metro Suppliers',
        'status' => 'active',
    ]);

    Payable::query()->create([
        'supplier_id' => $supplier->id,
        'invoice_number' => 'PO-1001',
        'amount_due' => 18000,
        'amount_paid' => 12000,
        'balance' => 6000,
        'status' => 'partial',
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $response = $this->actingAs($user)->get(route('payables.index'));

    $response->assertOk()
        ->assertSee('Payables Management')
        ->assertSee('Metro Suppliers');
});

it('allows staff to record a payable payment', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create([
        'name' => 'Apex Supply Co',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post(route('payables.store'), [
        'supplier_id' => $supplier->id,
        'invoice_number' => 'PO-1002',
        'amount_due' => 25000,
        'amount_paid' => 10000,
        'balance' => 15000,
        'status' => 'partial',
        'due_date' => now()->addDays(14)->toDateString(),
    ]);

    $response->assertRedirect(route('payables.index'))
        ->assertSessionHas('status', 'Payable saved successfully.');

    expect(Payable::query()->where('invoice_number', 'PO-1002')->count())->toBe(1);
});
