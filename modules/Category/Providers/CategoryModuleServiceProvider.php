<?php

namespace modules\Category\Providers;

use Illuminate\Support\ServiceProvider;
use modules\Category\Interfaces\CategoryInterface;
use modules\Category\Repositories\CategoryRepository;

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
