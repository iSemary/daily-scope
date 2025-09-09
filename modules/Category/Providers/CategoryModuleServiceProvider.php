<?php

namespace Modules\Category\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Category\Interfaces\CategoryInterface;
use Modules\Category\Repositories\CategoryRepository;

class CategoryModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CategoryInterface::class, CategoryRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
