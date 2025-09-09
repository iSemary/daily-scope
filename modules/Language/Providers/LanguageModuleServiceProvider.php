<?php

namespace modules\Language\Providers;

use Illuminate\Support\ServiceProvider;
use modules\Language\Interfaces\LanguageInterface;
use modules\Language\Repositories\LanguageRepository;

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
