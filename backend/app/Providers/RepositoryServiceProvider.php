<?php

namespace App\Providers;

use App\Domain\FeatureFlags\FeatureFlagRepositoryInterface;
use App\Domain\Reports\CarReportRepositoryInterface;
use App\Infrastructure\Repositories\CarReportRepository;
use App\Infrastructure\Repositories\FeatureFlagRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            FeatureFlagRepositoryInterface::class,
            FeatureFlagRepository::class
        );

        $this->app->bind(
            CarReportRepositoryInterface::class,
            CarReportRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
