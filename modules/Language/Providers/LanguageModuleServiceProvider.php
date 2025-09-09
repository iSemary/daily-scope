<?php

namespace Modules\Language\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Language\Interfaces\LanguageInterface;
use Modules\Language\Repositories\LanguageRepository;

class LanguageModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LanguageInterface::class, LanguageRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
