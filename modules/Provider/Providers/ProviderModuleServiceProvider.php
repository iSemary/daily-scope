<?php

namespace Modules\Provider\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Provider\Interfaces\ProviderInterface;
use Modules\Provider\Repositories\ProviderRepository;

class ProviderModuleServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(ProviderInterface::class, ProviderRepository::class);
    }

    public function boot(): void {}
}


