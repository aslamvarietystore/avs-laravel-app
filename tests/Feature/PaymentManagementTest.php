<?php

use App\Models\Payment;
use App\Models\Supplier;
use App\Models\User;

it('shows the payment management screen with recorded payments', function () {
    $user = User::factory()->create();
    $vendor = Supplier::factory()->create([
        'name' => 'Metro Suppliers',
        'status' => 'active',
    ]);

    Payment::query()->create([
        'vendor_id' => $vendor->id,
        'amount' => 18000,
        'method' => 'bank',
        'paid_on' => now()->toDateString(),
        'status' => 'paid',
        'reference' => 'BANK-101',
    ]);

    $response = $this->actingAs($user)->get(route('payments.index'));

    $response->assertOk()
        ->assertSee('Payment Management')
        ->assertSee('Metro Suppliers');
});

it('allows staff to create a payment record', function () {
    $user = User::factory()->create();
    $vendor = Supplier::factory()->create([
        'name' => 'Apex Supply Co',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post(route('payments.store'), [
        'vendor_id' => $vendor->id,
        'amount' => 25000,
        'method' => 'cash',
        'paid_on' => now()->toDateString(),
        'status' => 'paid',
        'reference' => 'CASH-202',
        'notes' => 'Advance payment',
    ]);

    $response->assertRedirect(route('payments.index'))
        ->assertSessionHas('status', 'Payment saved successfully.');

    expect(Payment::query()->where('reference', 'CASH-202')->count())->toBe(1);
});
