<?php

use App\Models\Supplier;
use App\Models\User;

it('shows the vendor management screen with supplier records', function () {
    $user = User::factory()->create();
    Supplier::factory()->create([
        'name' => 'Apex Imports',
        'phone' => '03001112233',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->get(route('vendors.index'));

    $response->assertOk()
        ->assertSee('Vendor Management')
        ->assertSee('Apex Imports');
});

it('allows staff to create a vendor record', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('vendors.store'), [
        'name' => 'Global Trade Hub',
        'contact_person' => 'Imran Ali',
        'phone' => '03005556677',
        'email' => 'sales@globaltradehub.com',
        'company_name' => 'Global Trade Hub',
        'city' => 'Lahore',
        'country' => 'Pakistan',
        'status' => 'active',
    ]);

    $response->assertRedirect(route('vendors.index'))
        ->assertSessionHas('status', 'Vendor created successfully.');

    expect(Supplier::query()->where('name', 'Global Trade Hub')->count())->toBe(1);
});
