<?php

use App\Models\Expense;
use App\Models\Supplier;
use App\Models\User;

it('shows the expense management screen with recorded expenses', function () {
    $user = User::factory()->create();
    $vendor = Supplier::factory()->create([
        'name' => 'Lahore Utilities',
        'status' => 'active',
    ]);

    Expense::query()->create([
        'vendor_id' => $vendor->id,
        'category' => 'Utilities',
        'description' => 'Electricity bill',
        'amount' => 12500,
        'incurred_on' => now()->toDateString(),
        'status' => 'paid',
    ]);

    $response = $this->actingAs($user)->get(route('expenses.index'));

    $response->assertOk()
        ->assertSee('Expense Management')
        ->assertSee('Lahore Utilities');
});

it('allows staff to create an expense entry', function () {
    $user = User::factory()->create();
    $vendor = Supplier::factory()->create([
        'name' => 'Metro Rent Co',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post(route('expenses.store'), [
        'vendor_id' => $vendor->id,
        'category' => 'Rent',
        'description' => 'Shop rent for September',
        'amount' => 45000,
        'incurred_on' => now()->toDateString(),
        'status' => 'paid',
    ]);

    $response->assertRedirect(route('expenses.index'))
        ->assertSessionHas('status', 'Expense saved successfully.');

    expect(Expense::query()->where('description', 'Shop rent for September')->count())->toBe(1);
});
