<?php

namespace Modules\Article\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Article\Interfaces\ArticleInterface;
use Modules\Article\Repositories\ArticleRepository;

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
