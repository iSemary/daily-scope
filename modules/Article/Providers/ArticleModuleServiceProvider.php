<?php

namespace modules\Article\Providers;

use Illuminate\Support\ServiceProvider;
use modules\Article\Interfaces\ArticleInterface;
use modules\Article\Repositories\ArticleRepository;

class ArticleModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ArticleInterface::class, ArticleRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
