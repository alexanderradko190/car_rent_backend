<?php

namespace App\Providers;

use App\Repositories\Car\CacheCarRepository;
use App\Repositories\Car\CarRepositoryInterface;
use App\Repositories\Car\EloquentCarRepository;
use Illuminate\Support\ServiceProvider;

class CarsServiceProvider extends ServiceProvider
{
    protected $app;

    public function register(): void
    {
        $this->app->bind(CarRepositoryInterface::class, function ($app) {
            return new CacheCarRepository(
                $app->make(EloquentCarRepository::class),
                $app['cache.store']
            );
        });
    }
}
