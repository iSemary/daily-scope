<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ApiVersionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerApiVersions();
    }

    /**
     * Register API versions
     */
    protected function registerApiVersions(): void
    {
        // API v1.0
        Route::prefix('api/v1.0')
            ->middleware('api')
            ->group(base_path('routes/v1.0/api.php'));

        // Future versions can be added here
        // Route::prefix('api/v2.0')
        //     ->middleware('api')
        //     ->group(base_path('routes/v2.0/api.php'));
    }
}
