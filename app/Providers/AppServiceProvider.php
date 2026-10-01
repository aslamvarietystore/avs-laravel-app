<?php

namespace App\Providers;

use App\Models\User;
use App\Services\ExchangeRateService;
use App\Services\PricingService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PricingService::class, fn () => new PricingService(app(ExchangeRateService::class)));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-products', function (User $user) {
            return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF], true);
        });

        Gate::define('view-internal-pricing', function (User $user) {
            return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF], true);
        });
    }
}
