<?php

namespace modules\Country\Providers;

use Illuminate\Support\ServiceProvider;
use modules\Country\Interfaces\CountryInterface;
use modules\Country\Repositories\CountryRepository;

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
