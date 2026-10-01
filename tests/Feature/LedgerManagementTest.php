<?php

use App\Models\LedgerEntry;
use App\Models\User;

it('shows the ledger screen with recorded entries', function () {
    $user = User::factory()->create();

    LedgerEntry::query()->create([
        'entry_date' => now()->toDateString(),
        'description' => 'Sale invoice issued',
        'type' => 'credit',
        'amount' => 15000,
        'reference' => 'INV-1001',
    ]);

    $response = $this->actingAs($user)->get(route('ledger.index'));

    $response->assertOk()
        ->assertSee('General Ledger')
        ->assertSee('Sale invoice issued');
});

it('allows staff to create a ledger entry', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ledger.store'), [
        'entry_date' => now()->toDateString(),
        'description' => 'Office supplies purchase',
        'type' => 'debit',
        'amount' => 3200,
        'reference' => 'OFF-2301',
    ]);

    $response->assertRedirect(route('ledger.index'))
        ->assertSessionHas('status', 'Ledger entry saved successfully.');

    expect(LedgerEntry::query()->where('reference', 'OFF-2301')->count())->toBe(1);
});
