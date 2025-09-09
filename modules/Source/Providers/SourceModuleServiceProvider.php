<?php

namespace Modules\Source\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Source\Interfaces\SourceInterface;
use Modules\Source\Repositories\SourceRepository;

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
