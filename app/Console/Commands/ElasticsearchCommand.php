<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElasticsearchService;
use Modules\Article\Entities\Article;

class ElasticsearchCommand extends Command
{
    protected $signature = 'elasticsearch:manage {action} {--index=articles}';
    protected $description = 'Manage Elasticsearch operations';

    private ElasticsearchService $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        parent::__construct();
        $this->elasticsearchService = $elasticsearchService;
    }

    public function handle(): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'create-index':
                return $this->createIndex();
            case 'reindex':
                return $this->reindex();
            case 'delete-index':
                return $this->deleteIndex();
            case 'status':
                return $this->status();
            default:
                $this->error('Invalid action. Available actions: create-index, reindex, delete-index, status');
                return 1;
        }
    }

    private function createIndex(): int
    {
        try {
            if ($this->elasticsearchService->indexExists()) {
                $this->warn('Index already exists. Use delete-index first if you want to recreate it.');
                return 0;
            }

            $this->elasticsearchService->createIndex();
            $this->info('Elasticsearch index created successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create index: ' . $e->getMessage());
            return 1;
        }
    }

    private function reindex(): int
    {
        try {
            if (!$this->elasticsearchService->indexExists()) {
                $this->info('Index does not exist. Creating it first...');
                $this->elasticsearchService->createIndex();
            }

            $this->info('Starting reindexing process...');
            
            $articles = Article::with(['source.category', 'source.country', 'source.language', 'source.provider', 'author'])
                ->chunk(100, function ($articles) {
                    foreach ($articles as $article) {
                        $articleData = $this->prepareArticleData($article);
                        $this->elasticsearchService->indexArticle($articleData);
                    }
                });

            $this->info('Reindexing completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to reindex: ' . $e->getMessage());
            return 1;
        }
    }

    private function deleteIndex(): int
    {
        try {
            if (!$this->elasticsearchService->indexExists()) {
                $this->warn('Index does not exist.');
                return 0;
            }

            $this->elasticsearchService->getClient()->indices()->delete([
                'index' => $this->elasticsearchService->getArticlesIndex()
            ]);

            $this->info('Elasticsearch index deleted successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to delete index: ' . $e->getMessage());
            return 1;
        }
    }

    private function status(): int
    {
        try {
            $exists = $this->elasticsearchService->indexExists();
            $this->info('Elasticsearch Status:');
            $this->info('Index exists: ' . ($exists ? 'Yes' : 'No'));
            
            if ($exists) {
                $stats = $this->elasticsearchService->getClient()->indices()->stats([
                    'index' => $this->elasticsearchService->getArticlesIndex()
                ]);
                
                $docCount = $stats['indices'][$this->elasticsearchService->getArticlesIndex()]['total']['docs']['count'];
                $this->info('Document count: ' . $docCount);
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to get status: ' . $e->getMessage());
            return 1;
        }
    }

    private function prepareArticleData(Article $article): array
    {
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
