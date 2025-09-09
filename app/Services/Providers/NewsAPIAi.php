<?php

namespace App\Services\Providers;

use App\Services\Abstractors\ProviderAbstractor;
use App\Services\Mappers\MapperFactory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Provider\Entities\Provider;
use Modules\Source\Entities\Source;
use Modules\Article\Entities\Article;
use Modules\Author\Entities\Author;
use Modules\Category\Entities\Category;
use Modules\Country\Entities\Country;
use Modules\Language\Entities\Language;

class NewsAPIAi extends ProviderAbstractor
{
    private Provider $provider;
    private string $endPoint;
    private string $apiKey;
    private array $countries = ['Germany', 'United State', 'Netherlands'];
    private $mapper;

    private const NEWS_PATH = 'article/getArticles';

    public function fetch()
    {
        Log::info("Starting NewsAPIAi fetch process", ['provider_id' => $this->provider->id]);

        try {
            $this->fetchArticles();

            Log::info("NewsAPIAi fetch process completed successfully", ['provider_id' => $this->provider->id]);
        } catch (\Exception $e) {
            Log::error("NewsAPIAi fetch process failed", [
                'provider_id' => $this->provider->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
        $this->setApiKey($provider->api_key);
        $this->setEndPoint($provider->end_point);
        $this->mapper = MapperFactory::create('NewsAPIAi');
    }

    protected function setApiKey(string $apiKey): void
    {
        try {
            $this->apiKey = Crypt::decrypt($apiKey);
        } catch (\Throwable $e) {
            $this->apiKey = $apiKey;
        }
    }

    protected function getApiKey(): string
    {
        return $this->apiKey;
    }

    protected function setEndPoint(string $endPoint): void
    {
        $this->endPoint = $endPoint;
    }

    protected function getEndPoint(): string
    {
        return $this->endPoint;
    }

    protected function fetchArticles(): void
    {
        foreach ($this->countries as $country) {
            $response = Http::timeout(30)->post(
                $this->endPoint . self::NEWS_PATH,
                [
                    'action' => 'getArticles',
                    'keyword' => $country,
                    'articlesPage' => 1,
                    'articlesCount' => 100,
                    'articlesSortBy' => 'date',
                    'articlesSortByAsc' => false,
                    'articlesArticleBodyLen' => -1,
                    'resultType' => 'articles',
                    'apiKey' => $this->apiKey,
                    'forceMaxDataTimeWindow' => 31
                ]
            );
            if ($response->successful()) {
                $data = $response->json();
                $fetchedArticles = $data['articles']['results'];
                $fetchedArticles['country'] = $country;
                $fetchedArticles['category'] = "General";
                $this->createOrUpdateArticles($fetchedArticles, false);
            } else {
                $errorCode = $response->status();
                $errorMessage = $response->body();
            }
        }
    }

    protected function createOrUpdateArticles(array $articles, bool $heading): void
    {
        if (isset($articles) && is_array($articles) && count($articles)) {
            $mappedCategory = $this->mapper->mapCategory($articles);
            $mappedCountry = $this->mapper->mapCountry($articles);
            
            $category = Category::updateOrCreate($mappedCategory);
            $country = Country::updateOrCreate($mappedCountry);
            
            foreach ($articles as $article) {
                if (isset($article['title'])) {
                    $mappedLanguage = $this->mapper->mapLanguage($article);
                    $mappedSource = $this->mapper->mapSource($article);
                    $mappedArticle = $this->mapper->mapArticle($article, $heading);
                    $mappedAuthor = $this->mapper->mapAuthor($article, null);

                    if (isset($article['lang'])) {
                        $language = Language::updateOrCreate($mappedLanguage);
                    }

                    $source = Source::updateOrCreate([
                        'slug' => $mappedSource['slug'],
                    ], [
                        'title' => $mappedSource['title'],
                        'provider_id' => $this->provider->id,
                        'url' => $mappedSource['url'],
                        'description' => $mappedSource['description'],
                        'category_id' => $category->id,
                        'country_id' => $country->id,
                        'language_id' => $language->id,
                    ]);

                    $author = Author::updateOrCreate(
                        $mappedAuthor,
                        ['source_id' => $source->id]
                    );

                    Article::updateOrCreate([
                        'slug' => $mappedArticle['slug']
                    ], [
                        'title' => $mappedArticle['title'],
                        'author_id' => $author->id,
                        'source_id' => $source->id,
                        'description' => $mappedArticle['description'],
                        'body' => $mappedArticle['body'],
                        'is_head' => $mappedArticle['is_head'],
                        'reference_url' => $mappedArticle['reference_url'],
                        'image' => $mappedArticle['image'],
                        'published_at' => $mappedArticle['published_at'],
                    ]);
                }
            }
        }
    }

    protected function createOrUpdateSources(array $sources): void {}

    protected function fetchSources(): void {}

    protected function fetchTopHeadingsSources(): void {}

    protected function fetchTopHeadingsArticles(): void {}
}
