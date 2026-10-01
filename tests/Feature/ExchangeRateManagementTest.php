<?php

use App\Models\ExchangeRate;
use App\Models\User;
use App\Services\PricingService;

it('shows the currency screen with the active exchange rate', function () {
    $user = User::factory()->create();
    ExchangeRate::factory()->create([
        'rate' => 41.50,
        'is_active' => true,
        'effective_date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($user)->get(route('exchange-rates.index'));

    $response->assertOk()
        ->assertSee('Currency')
        ->assertSee('41.50')
        ->assertSee('Record New Rate');
});

it('records a new active rate and retires the previous one', function () {
    $user = User::factory()->create();
    $previous = ExchangeRate::factory()->create([
        'rate' => 39.00,
        'is_active' => true,
        'effective_date' => now()->subWeek()->toDateString(),
    ]);

    $response = $this->actingAs($user)->post(route('exchange-rates.store'), [
        'currency_from' => 'CNY',
        'currency_to' => 'PKR',
        'rate' => 42.25,
        'effective_date' => now()->toDateString(),
        'is_active' => '1',
        'notes' => 'September settlement rate',
    ]);

    $response->assertRedirect(route('exchange-rates.index'))
        ->assertSessionHas('status', 'Exchange rate saved successfully.');

    expect($previous->refresh()->is_active)->toBeFalse();

    $activeRates = ExchangeRate::query()->where('is_active', true)->get();

    expect($activeRates)->toHaveCount(1)
        ->and((float) $activeRates->first()->rate)->toBe(42.25)
        ->and($activeRates->first()->created_by)->toBe($user->id);

    expect(app(PricingService::class)->getCurrentExchangeRate())->toBe(42.25);
});

it('can activate an older rate and retire the current one', function () {
    $user = User::factory()->create();
    $current = ExchangeRate::factory()->create([
        'rate' => 45.00,
        'is_active' => true,
        'effective_date' => now()->toDateString(),
    ]);
    $older = ExchangeRate::factory()->create([
        'rate' => 38.00,
        'is_active' => false,
        'effective_date' => now()->subMonth()->toDateString(),
    ]);

    $response = $this->actingAs($user)->post(route('exchange-rates.activate', $older));

    $response->assertRedirect(route('exchange-rates.index'))
        ->assertSessionHas('status', 'Exchange rate activated successfully.');

    expect($older->refresh()->is_active)->toBeTrue()
        ->and($current->refresh()->is_active)->toBeFalse();
});

it('refuses to remove the active rate', function () {
    $user = User::factory()->create();
    $active = ExchangeRate::factory()->create(['is_active' => true]);

    $response = $this->actingAs($user)->delete(route('exchange-rates.destroy', $active));

    $response->assertRedirect(route('exchange-rates.index'))
        ->assertSessionHasErrors('rate');

    expect(ExchangeRate::query()->whereKey($active->id)->exists())->toBeTrue();
});

it('removes a historical rate', function () {
    $user = User::factory()->create();
    $historical = ExchangeRate::factory()->create(['is_active' => false, 'rate' => 36.00]);

    $response = $this->actingAs($user)->delete(route('exchange-rates.destroy', $historical));

    $response->assertRedirect(route('exchange-rates.index'))
        ->assertSessionHas('status', 'Exchange rate removed successfully.');

    expect(ExchangeRate::query()->whereKey($historical->id)->exists())->toBeFalse();
});
