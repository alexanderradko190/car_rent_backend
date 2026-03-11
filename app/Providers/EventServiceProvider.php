<?php

namespace App\Providers;

use App\Events\Car\CarChanged;
use App\Events\RentalRequest\RentalRequestIsCompleted;
use App\Listeners\Car\RebuildCarsAllCache;
use App\Listeners\RentalRequest\SendRentalCompletedEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CarChanged::class => [
            RebuildCarsAllCache::class,
        ],

        RentalRequestIsCompleted::class => [
            SendRentalCompletedEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
