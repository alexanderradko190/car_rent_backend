<?php

namespace App\Providers;

use App\Models\User\User;
use App\Observers\RoleObserver;
use App\Services\RentalCostCalculator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RentalCostCalculator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(RoleObserver::class);
    }
}
