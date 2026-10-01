<?php

use App\Models\Customer;
use App\Models\User;

it('shows the customer management screen with customer records', function () {
    $user = User::factory()->create();

    Customer::factory()->create([
        'name' => 'Ayesha Traders',
        'phone' => '03001234567',
        'email' => 'ayesha@example.com',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->get(route('customers.index'));

    $response->assertOk()
        ->assertSee('Customer Management')
        ->assertSee('Ayesha Traders')
        ->assertSee('03001234567');
});

it('allows staff to create a new customer from the customer screen', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('customers.store'), [
        'name' => 'Wholesale Hub',
        'phone' => '03123456789',
        'whatsapp' => '03123456789',
        'email' => 'hub@example.com',
        'company_name' => 'Wholesale Hub Ltd',
        'city' => 'Karachi',
        'country' => 'Pakistan',
        'status' => 'active',
    ]);

    $response->assertRedirect(route('customers.index'))
        ->assertSessionHas('status', 'Customer created successfully.');

    $this->assertDatabaseHas('customers', [
        'name' => 'Wholesale Hub',
        'email' => 'hub@example.com',
    ]);
});
