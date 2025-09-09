<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder as ElasticsearchClientBuilder;
use Elastic\Elasticsearch\Client;
use Illuminate\Support\Facades\Config;

class ElasticsearchService
{
    private Client $client;
    private string $articlesIndex;

    public function __construct()
    {
        $this->client = ElasticsearchClientBuilder::create()
            ->setHosts(Config::get('elasticsearch.hosts'))
            ->setRetries(Config::get('elasticsearch.retries', 2))
            ->build();

        $this->articlesIndex = Config::get('elasticsearch.index.articles');
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getArticlesIndex(): string
    {
        return $this->articlesIndex;
    }

    public function indexArticle(array $articleData): array
    {
        $response = $this->client->index([
            'index' => $this->articlesIndex,
            'id' => $articleData['id'],
            'body' => $articleData
        ]);
        
        return $response->asArray();
    }

    public function updateArticle(int $id, array $articleData): array
    {
        $response = $this->client->update([
            'index' => $this->articlesIndex,
            'id' => $id,
            'body' => [
                'doc' => $articleData
            ]
        ]);
        
        return $response->asArray();
    }

    public function deleteArticle(int $id): array
    {
        $response = $this->client->delete([
            'index' => $this->articlesIndex,
            'id' => $id
        ]);
        
        return $response->asArray();
    }

    public function searchArticles(array $searchParams): array
    {
        $query = $this->buildSearchQuery($searchParams);
        
        $response = $this->client->search([
            'index' => $this->articlesIndex,
            'body' => $query
        ]);
        
        return $response->asArray();
    }

    public function createIndex(): array
    {
        $indexParams = [
            'index' => $this->articlesIndex,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'custom_text_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase', 'stop', 'snowball']
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'custom_text_analyzer',
                            'fields' => [
                                'keyword' => ['type' => 'keyword']
                            ]
                        ],
                        'slug' => ['type' => 'keyword'],
                        'description' => [
                            'type' => 'text',
                            'analyzer' => 'custom_text_analyzer'
                        ],
                        'body' => [
                            'type' => 'text',
                            'analyzer' => 'custom_text_analyzer'
                        ],
                        'reference_url' => ['type' => 'keyword'],
                        'image' => ['type' => 'keyword'],
                        'is_head' => ['type' => 'boolean'],
                        'source_id' => ['type' => 'integer'],
                        'author_id' => ['type' => 'integer'],
                        'category_id' => ['type' => 'integer'],
                        'published_at' => ['type' => 'date'],
                        'source' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => ['type' => 'integer'],
                                'title' => ['type' => 'text'],
                                'slug' => ['type' => 'keyword'],
                                'description' => ['type' => 'text'],
                                'provider_id' => ['type' => 'integer'],
                                'category_id' => ['type' => 'integer'],
                                'country_id' => ['type' => 'integer'],
                                'language_id' => ['type' => 'integer']
                            ]
                        ],
                        'author' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => ['type' => 'integer'],
                                'name' => ['type' => 'text'],
                                'slug' => ['type' => 'keyword']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->client->indices()->create($indexParams);
        return $response->asArray();
    }

    public function indexExists(): bool
    {
        $response = $this->client->indices()->exists(['index' => $this->articlesIndex]);
        return $response->asBool();
    }

    private function buildSearchQuery(array $searchParams): array
    {
        $keyword = $searchParams['keyword'];
        $categoryId = $searchParams['category_id'] ?? null;
        $sourceId = $searchParams['source_id'] ?? null;
        $dateOrder = $searchParams['date_order'] ?? 'desc';
        $page = $searchParams['page'] ?? 1;
        $perPage = $searchParams['per_page'] ?? 20;
        $searchInBody = $searchParams['search_in_body'] ?? false;

        $mustQueries = [];
        $shouldQueries = [];

        // Text search
        $searchFields = ['title^3', 'description^2'];
        if ($searchInBody) {
            $searchFields[] = 'body';
        }

        $shouldQueries[] = [
            'multi_match' => [
                'query' => $keyword,
                'fields' => $searchFields,
                'type' => 'best_fields',
                'fuzziness' => 'AUTO'
            ]
        ];

        // Filters
        if ($categoryId) {
            $mustQueries[] = [
                'term' => ['category_id' => $categoryId]
            ];
        }

        if ($sourceId) {
            $mustQueries[] = [
                'term' => ['source_id' => $sourceId]
            ];
        }

        $query = [
            'bool' => [
                'must' => $mustQueries,
                'should' => $shouldQueries,
                'minimum_should_match' => 1
            ]
        ];

        return [
            'query' => $query,
            'sort' => [
                ['published_at' => ['order' => $dateOrder === 'desc' ? 'desc' : 'asc']]
            ],
            'from' => ($page - 1) * $perPage,
            'size' => $perPage,
            'highlight' => [
                'fields' => [
                    'title' => ['fragment_size' => 150],
                    'description' => ['fragment_size' => 150],
                    'body' => ['fragment_size' => 150]
                ]
            ]
        ];
    }
}
