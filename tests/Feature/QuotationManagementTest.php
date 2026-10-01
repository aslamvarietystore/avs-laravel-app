<?php

use App\Models\Customer;
use App\Models\Quotation;
use App\Models\User;

it('shows the quotation management screen with recorded quotations', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create([
        'name' => 'Ayesha Traders',
        'status' => 'active',
    ]);

    Quotation::query()->create([
        'customer_id' => $customer->id,
        'quotation_number' => 'QTN-1001',
        'issued_on' => now()->toDateString(),
        'valid_until' => now()->addDays(10)->toDateString(),
        'subtotal' => 12000,
        'status' => 'approved',
        'notes' => 'Seasonal pricing',
    ]);

    $response = $this->actingAs($user)->get(route('quotations.index'));

    $response->assertOk()
        ->assertSee('Quotation Management')
        ->assertSee('QTN-1001');
});

it('allows staff to create a quotation entry', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create([
        'name' => 'Wholesale Hub',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post(route('quotations.store'), [
        'customer_id' => $customer->id,
        'quotation_number' => 'QTN-1002',
        'issued_on' => now()->toDateString(),
        'valid_until' => now()->addDays(15)->toDateString(),
        'subtotal' => 24000,
        'status' => 'pending',
        'notes' => 'Custom bundle pricing',
    ]);

    $response->assertRedirect(route('quotations.index'))
        ->assertSessionHas('status', 'Quotation saved successfully.');

    expect(Quotation::query()->where('quotation_number', 'QTN-1002')->count())->toBe(1);
});
