<?php

namespace App\Providers;

use App\Repositories\Report\EloquentReportRepository;
use App\Repositories\Report\ReportRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ReportServiceProvider extends ServiceProvider
{
    protected $app;

    public function register(): void
    {
        $this->app->bind(ReportRepositoryInterface::class, EloquentReportRepository::class);
    }
}
