<?php

use App\Models\CashflowEntry;
use App\Models\User;

it('shows the cashbook screen with recorded cash entries', function () {
    $user = User::factory()->create();

    CashflowEntry::query()->create([
        'entry_date' => now()->toDateString(),
        'description' => 'Opening balance',
        'type' => 'in',
        'amount' => 50000,
        'category' => 'Opening Balance',
        'reference' => 'OPENING-001',
    ]);

    $response = $this->actingAs($user)->get(route('cashbook.index'));

    $response->assertOk()
        ->assertSee('Cashbook')
        ->assertSee('Opening balance');
});

it('allows staff to create a cashbook entry', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('cashbook.store'), [
        'entry_date' => now()->toDateString(),
        'description' => 'Shop rent payment',
        'type' => 'out',
        'amount' => 45000,
        'category' => 'Rent',
        'reference' => 'RENT-001',
    ]);

    $response->assertRedirect(route('cashbook.index'))
        ->assertSessionHas('status', 'Cashbook entry saved successfully.');

    expect(CashflowEntry::query()->where('reference', 'RENT-001')->count())->toBe(1);
});
