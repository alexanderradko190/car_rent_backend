<?php

namespace App\Listeners;

use App\Events\CarChanged;
use App\Models\Car\Car;
use Illuminate\Contracts\Cache\Repository as Cache;

class RebuildCarsAllCache
{
    private Cache $cache;
    private bool $cacheIsOn;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
        $this->cacheIsOn = config('cache.on');
    }
    public function handle(CarChanged $event): void
    {
        if (!$this->cacheIsOn) {
            return;
        }

        $cacheKey = 'cars_all';

        $this->cache->forget($cacheKey);

        $this->cache->rememberForever($cacheKey, function () {
            return Car::query()
                ->with('renter')
                ->get()
                ->toArray();
        });
    }
}
