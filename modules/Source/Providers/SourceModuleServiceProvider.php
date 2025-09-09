<?php

namespace modules\Source\Providers;

use Illuminate\Support\ServiceProvider;
use modules\Source\Interfaces\SourceInterface;
use modules\Source\Repositories\SourceRepository;

class SourceModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SourceInterface::class, SourceRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
