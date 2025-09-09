<?php

namespace Modules\Author\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Author\Interfaces\AuthorInterface;
use Modules\Author\Repositories\AuthorRepository;

class AuthorModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthorInterface::class, AuthorRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
