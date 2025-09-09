<?php

namespace modules\Provider\Providers;

use Illuminate\Support\ServiceProvider;
use modules\Provider\Interfaces\ProviderInterface;
use modules\Provider\Repositories\ProviderRepository;

class ProviderModuleServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(ProviderInterface::class, ProviderRepository::class);
    }

    public function boot(): void {}
}


