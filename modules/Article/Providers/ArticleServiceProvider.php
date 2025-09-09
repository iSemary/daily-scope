<?php

namespace Modules\Article\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Article\Interfaces\ArticleInterface;
use Modules\Article\Repositories\ArticleRepository;
use Modules\Article\Repositories\ElasticsearchArticleRepository;
use Modules\Article\Observers\ArticleObserver;
use Modules\Article\Entities\Article;
use App\Services\ElasticsearchService;

class ArticleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ElasticsearchService::class, function ($app) {
            return new ElasticsearchService();
        });

        $this->app->bind(ArticleInterface::class, function ($app) {
            $useElasticsearch = config('elasticsearch.enabled', false);
            
            if ($useElasticsearch) {
                return new ElasticsearchArticleRepository($app->make(ElasticsearchService::class));
            }
            
            return new ArticleRepository();
        });
    }

    public function boot(): void
    {
        if (config('elasticsearch.enabled', false)) {
            Article::observe(ArticleObserver::class);
        }
    }
}
