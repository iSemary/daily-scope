<?php

namespace App\Providers;

use App\Interfaces\HomeServiceInterface;
use App\Repositories\HomeRepository;
use App\Services\HomeService;
use Illuminate\Support\ServiceProvider;

class HomeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(HomeRepository::class, function ($app) {
            return new HomeRepository();
        });

        $this->app->bind(HomeServiceInterface::class, function ($app) {
            return new HomeService($app->make(HomeRepository::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
