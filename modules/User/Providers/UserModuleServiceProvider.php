<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\User\Interfaces\UserInterface;
use Modules\User\Repositories\UserRepository;

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
