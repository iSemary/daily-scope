<?php

namespace modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use modules\User\Interfaces\UserInterface;
use modules\User\Repositories\UserRepository;

class UserModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
