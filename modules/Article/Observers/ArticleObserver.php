<?php

namespace Modules\Article\Observers;

use Modules\Article\Entities\Article;
use App\Services\ElasticsearchService;
use Illuminate\Support\Facades\Log;

class ArticleObserver
{
    private ElasticsearchService $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    public function created(Article $article): void
    {
        if (config('elasticsearch.enabled', false)) {
            $this->indexArticle($article);
        }
    }

    public function updated(Article $article): void
    {
        if (config('elasticsearch.enabled', false)) {
            $this->indexArticle($article);
        }
    }

    public function deleted(Article $article): void
    {
        if (config('elasticsearch.enabled', false)) {
            try {
                $this->elasticsearchService->deleteArticle($article->id);
            } catch (\Exception $e) {
                Log::error('Failed to delete article from Elasticsearch: ' . $e->getMessage());
            }
        }
    }

    private function indexArticle(Article $article): void
    {
        try {
            $articleData = $this->prepareArticleData($article);
            $this->elasticsearchService->indexArticle($articleData);
        } catch (\Exception $e) {
            Log::error('Failed to index article in Elasticsearch: ' . $e->getMessage());
        }
    }

    private function prepareArticleData(Article $article): array
    {
        $article->load(['source.category', 'source.country', 'source.language', 'source.provider', 'author']);

        return [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'description' => $article->description,
            'reference_url' => $article->reference_url,
            'body' => $article->body,
            'image' => $article->image,
            'is_head' => $article->is_head,
            'source_id' => $article->source_id,
            'author_id' => $article->author_id,
            'category_id' => $article->source?->category_id,
            'published_at' => $article->published_at,
            'source' => $article->source ? [
                'id' => $article->source->id,
                'title' => $article->source->title,
                'slug' => $article->source->slug,
                'description' => $article->source->description,
                'provider_id' => $article->source->provider_id,
                'category_id' => $article->source->category_id,
                'country_id' => $article->source->country_id,
                'language_id' => $article->source->language_id,
                'category' => $article->source->category ? [
                    'id' => $article->source->category->id,
                    'title' => $article->source->category->title,
                    'slug' => $article->source->category->slug,
                ] : null,
                'country' => $article->source->country ? [
                    'id' => $article->source->country->id,
                    'name' => $article->source->country->name,
                    'code' => $article->source->country->code,
                ] : null,
                'language' => $article->source->language ? [
                    'id' => $article->source->language->id,
                    'name' => $article->source->language->name,
                    'code' => $article->source->language->code,
                ] : null,
                'provider' => $article->source->provider ? [
                    'id' => $article->source->provider->id,
                    'name' => $article->source->provider->name,
                ] : null,
            ] : null,
            'author' => $article->author ? [
                'id' => $article->author->id,
                'name' => $article->author->name,
                'slug' => $article->author->slug,
            ] : null,
        ];
    }
}
