<?php

use App\Models\Customer;
use App\Models\Receivable;
use App\Models\User;

it('shows the receivables screen with outstanding balances', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create([
        'name' => 'Ayesha Traders',
        'status' => 'active',
    ]);

    Receivable::query()->create([
        'customer_id' => $customer->id,
        'invoice_number' => 'INV-1001',
        'amount_due' => 15000,
        'amount_received' => 8000,
        'balance' => 7000,
        'status' => 'partial',
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $response = $this->actingAs($user)->get(route('receivables.index'));

    $response->assertOk()
        ->assertSee('Receivables Management')
        ->assertSee('Ayesha Traders');
});

it('allows staff to record a receivable collection', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create([
        'name' => 'Wholesale Hub',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post(route('receivables.store'), [
        'customer_id' => $customer->id,
        'invoice_number' => 'INV-1002',
        'amount_due' => 25000,
        'amount_received' => 10000,
        'balance' => 15000,
        'status' => 'partial',
        'due_date' => now()->addDays(14)->toDateString(),
    ]);

    $response->assertRedirect(route('receivables.index'))
        ->assertSessionHas('status', 'Receivable saved successfully.');

    expect(Receivable::query()->where('invoice_number', 'INV-1002')->count())->toBe(1);
});
