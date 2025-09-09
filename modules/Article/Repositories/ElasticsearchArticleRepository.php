<?php

namespace Modules\Article\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Carbon\Carbon;
use Modules\Article\Entities\Article;
use Modules\Article\Interfaces\ArticleInterface;
use App\Services\ElasticsearchService;

class ElasticsearchArticleRepository implements ArticleInterface
{
    private ElasticsearchService $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    public function all(): Collection
    {
        $response = $this->elasticsearchService->getClient()->search([
            'index' => $this->elasticsearchService->getArticlesIndex(),
            'body' => [
                'query' => ['match_all' => []],
                'size' => 1000
            ]
        ]);

        $data = $response->asArray();
        return $this->convertToCollection($data['hits']['hits']);
    }

    public function findById(int $id): ?Article
    {
        try {
            $response = $this->elasticsearchService->getClient()->get([
                'index' => $this->elasticsearchService->getArticlesIndex(),
                'id' => $id
            ]);

            $data = $response->asArray();
            return $this->convertToArticle($data['_source']);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findBySourceAndArticleSlug(string $sourceSlug, string $articleSlug): ?Article
    {
        $response = $this->elasticsearchService->getClient()->search([
            'index' => $this->elasticsearchService->getArticlesIndex(),
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['source.slug' => $sourceSlug]],
                            ['term' => ['slug' => $articleSlug]]
                        ]
                    ]
                ],
                'size' => 1
            ]
        ]);

        $data = $response->asArray();
        if (empty($data['hits']['hits'])) {
            return null;
        }

        return $this->convertToArticle($data['hits']['hits'][0]['_source']);
    }

    public function search(string $keyword, ?int $categoryId = null, ?int $sourceId = null, string $dateOrder = 'desc'): LengthAwarePaginator
    {
        $searchParams = [
            'keyword' => $keyword,
            'category_id' => $categoryId,
            'source_id' => $sourceId,
            'date_order' => $dateOrder,
            'search_in_body' => false
        ];

        $response = $this->elasticsearchService->searchArticles($searchParams);
        
        return $this->convertToPaginator($response, $searchParams);
    }

    public function searchDeeply(string $keyword): LengthAwarePaginator
    {
        $searchParams = [
            'keyword' => $keyword,
            'search_in_body' => true
        ];

        $response = $this->elasticsearchService->searchArticles($searchParams);
        
        return $this->convertToPaginator($response, $searchParams);
    }

    public function getTodayArticles(): LengthAwarePaginator
    {
        $today = Carbon::today();
        $startOfDay = $today->startOfDay()->timestamp;
        $endOfDay = $today->endOfDay()->timestamp;

        $response = $this->elasticsearchService->getClient()->search([
            'index' => $this->elasticsearchService->getArticlesIndex(),
            'body' => [
                'query' => [
                    'range' => [
                        'published_at' => [
                            'gte' => $startOfDay,
                            'lte' => $endOfDay
                        ]
                    ]
                ],
                'sort' => [['published_at' => ['order' => 'desc']]],
                'size' => 20
            ]
        ]);

        $data = $response->asArray();
        return $this->convertToPaginator($data, ['per_page' => 20]);
    }

    public function getBySourceAndAuthorSlug(string $sourceSlug, string $authorSlug): LengthAwarePaginator
    {
        $response = $this->elasticsearchService->getClient()->search([
            'index' => $this->elasticsearchService->getArticlesIndex(),
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['source.slug' => $sourceSlug]],
                            ['term' => ['author.slug' => $authorSlug]]
                        ]
                    ]
                ],
                'sort' => [['published_at' => ['order' => 'desc']]],
                'size' => 20
            ]
        ]);

        $data = $response->asArray();
        return $this->convertToPaginator($data, ['per_page' => 20]);
    }

    public function getByRelatedItemSlug(string $slug, int $itemType, string $itemKey): LengthAwarePaginator
    {
        $field = $this->getFieldByItemType($itemType, $itemKey);
        
        $response = $this->elasticsearchService->getClient()->search([
            'index' => $this->elasticsearchService->getArticlesIndex(),
            'body' => [
                'query' => [
                    'term' => [$field => $slug]
                ],
                'sort' => [['published_at' => ['order' => 'desc']]],
                'size' => 20
            ]
        ]);

        $data = $response->asArray();
        return $this->convertToPaginator($data, ['per_page' => 20]);
    }

    public function getRelatedArticles(int $categoryId, int $sourceId, int $limit = 8): Collection
    {
        $response = $this->elasticsearchService->getClient()->search([
            'index' => $this->elasticsearchService->getArticlesIndex(),
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['category_id' => $categoryId]],
                            ['term' => ['source_id' => $sourceId]]
                        ]
                    ]
                ],
                'sort' => ['_script' => [
                    'type' => 'number',
                    'script' => ['source' => 'Math.random()']
                ]],
                'size' => $limit
            ]
        ]);

        $data = $response->asArray();
        return $this->convertToCollection($data['hits']['hits']);
    }

    private function convertToCollection(array $hits): Collection
    {
        $articles = collect();
        
        foreach ($hits as $hit) {
            $article = $this->convertToArticle($hit['_source']);
            if ($article) {
                $articles->push($article);
            }
        }

        return $articles;
    }

    private function convertToArticle(array $source): ?Article
    {
        try {
            $article = new Article();
            $article->id = $source['id'];
            $article->title = $source['title'];
            $article->slug = $source['slug'];
            $article->description = $source['description'];
            $article->reference_url = $source['reference_url'] ?? null;
            $article->body = $source['body'] ?? null;
            $article->image = $source['image'] ?? null;
            $article->is_head = $source['is_head'] ?? false;
            $article->source_id = $source['source_id'];
            $article->author_id = $source['author_id'];
            $article->published_at = $source['published_at'];

            // Set relations
            if (isset($source['source'])) {
                $article->setRelation('source', (object) $source['source']);
            }
            if (isset($source['author'])) {
                $article->setRelation('author', (object) $source['author']);
            }

            return $article;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function convertToPaginator(array $response, array $searchParams): LengthAwarePaginator
    {
        $hits = $response['hits']['hits'];
        $total = $response['hits']['total']['value'];
        $perPage = $searchParams['per_page'] ?? 20;
        $currentPage = $searchParams['page'] ?? 1;

        $articles = $this->convertToCollection($hits);

        return new Paginator(
            $articles,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    private function getFieldByItemType(int $itemType, string $itemKey): string
    {
        $fieldMap = [
            1 => 'source.slug',      // Source
            2 => 'author.slug',      // Author
            3 => 'source.category.slug', // Category
        ];

        return $fieldMap[$itemType] ?? 'source.slug';
    }
}
