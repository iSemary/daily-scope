<?php

namespace Modules\Country\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Country\Interfaces\CountryInterface;
use Modules\Country\Repositories\CountryRepository;

class CountryModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CountryInterface::class, CountryRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
