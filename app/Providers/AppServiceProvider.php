<?php

namespace App\Providers;

use App\Helpers\TransactionHelper;
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
        $this->app->singleton(TransactionHelper::class);
    }
}
