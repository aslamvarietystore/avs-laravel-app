<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;

it('shows the invoice management screen with recorded invoices', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create([
        'name' => 'Ayesha Traders',
        'status' => 'active',
    ]);

    Invoice::query()->create([
        'customer_id' => $customer->id,
        'invoice_number' => 'INV-1001',
        'issued_on' => now()->toDateString(),
        'due_date' => now()->addDays(7)->toDateString(),
        'total_amount' => 15000,
        'status' => 'paid',
        'notes' => 'Wholesale invoice',
    ]);

    $response = $this->actingAs($user)->get(route('invoices.index'));

    $response->assertOk()
        ->assertSee('Invoice Management')
        ->assertSee('INV-1001');
});

it('allows staff to create an invoice entry', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create([
        'name' => 'Wholesale Hub',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post(route('invoices.store'), [
        'customer_id' => $customer->id,
        'invoice_number' => 'INV-1002',
        'issued_on' => now()->toDateString(),
        'due_date' => now()->addDays(14)->toDateString(),
        'total_amount' => 25000,
        'status' => 'pending',
        'notes' => 'Credit terms',
    ]);

    $response->assertRedirect(route('invoices.index'))
        ->assertSessionHas('status', 'Invoice saved successfully.');

    expect(Invoice::query()->where('invoice_number', 'INV-1002')->count())->toBe(1);
});
