<?php

namespace modules\Author\Providers;

use Illuminate\Support\ServiceProvider;
use modules\Author\Interfaces\AuthorInterface;
use modules\Author\Repositories\AuthorRepository;

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
