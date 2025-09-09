<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\User\Interfaces\UserInterface;
use Modules\User\Interfaces\AuthInterface;
use Modules\User\Repositories\UserRepository;
use Modules\User\Repositories\AuthRepository;

class UserModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(AuthInterface::class, AuthRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
