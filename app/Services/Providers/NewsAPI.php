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

class NewsAPI extends ProviderAbstractor
{
    private Provider $provider;
    private string $endPoint;
    private string $apiKey;
    private array $countries = ['de', 'us', 'nl'];
    private $mapper;

    private const SOURCES_PATH = '/sources';
    private const EVERYTHING_PATH = '/everything';
    private const TOP_HEADLINES_SOURCES_PATH = '/top-headlines/sources';
    private const TOP_HEADLINES_PATH = '/top-headlines';

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
        $this->setApiKey($provider->api_key);
        $this->setEndPoint($provider->end_point);
        $this->mapper = MapperFactory::create('NewsAPI');
    }

    public function fetch()
    {
        Log::info("Starting NewsAPI fetch process", ['provider_id' => $this->provider->id]);

        try {
            $this->fetchSources();
            $this->fetchTopHeadingsSources();
            $this->fetchArticles();
            $this->fetchTopHeadingsArticles();

            Log::info("NewsAPI fetch process completed successfully", ['provider_id' => $this->provider->id]);
        } catch (\Exception $e) {
            Log::error("NewsAPI fetch process failed", [
                'provider_id' => $this->provider->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function setApiKey(string $apiKey): void
    {
        try {
            $this->apiKey = Crypt::decrypt($apiKey);
        } catch (\Throwable $e) {
            // Fallback for legacy plaintext keys
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

    protected function fetchSources(): void
    {
        foreach ($this->countries as $country) {
            $response = Http::timeout(30)->get($this->endPoint . self::SOURCES_PATH, ['apiKey' => $this->apiKey, 'country' => $country]);
            if ($response->successful()) {
                $data = $response->json();
                $fetchedSources = $data['sources'];
                $this->createOrUpdateSources($fetchedSources);
            } else {
                $errorCode = $response->status();
                $errorMessage = $response->body();
            }
        }
    }

    protected function fetchArticles(): void
    {
        $sources = Source::where("provider_id", $this->provider->id)->get();
        foreach ($sources as $source) {
            $response = Http::timeout(30)->get($this->endPoint . self::EVERYTHING_PATH, ['apiKey' => $this->apiKey, 'sources' => $source->slug]);
            if ($response->successful()) {
                $data = $response->json();
                $fetchedArticles = [];
                $fetchedArticles['articles'] = $data['articles'];
                $fetchedArticles['source'] = $source;
                $this->createOrUpdateArticles($fetchedArticles, false);
            } else {
                $errorCode = $response->status();
                $errorMessage = $response->body();
            }
        }
    }

    protected function fetchTopHeadingsSources(): void
    {
        $response = Http::timeout(30)->get($this->endPoint . self::TOP_HEADLINES_SOURCES_PATH, ['apiKey' => $this->apiKey]);
        if ($response->successful()) {
            $data = $response->json();
            $fetchedSources = $data['sources'];
            $this->createOrUpdateSources($fetchedSources, true);
        } else {
            $errorCode = $response->status();
            $errorMessage = $response->body();
        }
    }

    protected function fetchTopHeadingsArticles(): void
    {
        foreach ($this->countries as $country) {
            $response = Http::timeout(30)->get($this->endPoint . self::TOP_HEADLINES_PATH, ['apiKey' => $this->apiKey, 'country' => $country]);
            if ($response->successful()) {
                $data = $response->json();
                $fetchedArticles = [];
                $fetchedArticles['articles'] = $data['articles'];
                $this->createOrUpdateArticles($fetchedArticles, true);
            } else {
                $errorCode = $response->status();
                $errorMessage = $response->body();
            }
        }
    }

    protected function createOrUpdateArticles(array $articles, bool $heading): void
    {
        if (isset($articles['articles']) && is_array($articles['articles']) && count($articles['articles'])) {
            foreach ($articles['articles'] as $article) {
                $source = isset($articles['source']) ? $articles['source'] : Source::where("slug", $article['source']['id'])->where("provider_id", $this->provider->id)->first();
                if (isset($article['content'])) {
                    $mappedArticle = $this->mapper->mapArticle($article, $source, $heading);
                    $mappedAuthor = $this->mapper->mapAuthor($article, $source);
                    
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
                        'provider_id' => $this->provider->id,
                        'category_id' => $source->category_id,
                        'language_id' => $source->language_id,
                        'country_id' => $source->country_id,
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

    protected function createOrUpdateSources(array $sources): void
    {
        if (isset($sources) && is_array($sources) && count($sources)) {
            foreach ($sources as $source) {
                $mappedSource = $this->mapper->mapSource($source);
                $mappedCategory = $this->mapper->mapCategory($source);
                $mappedCountry = $this->mapper->mapCountry($source);
                $mappedLanguage = $this->mapper->mapLanguage($source);

                if (isset($source['category'])) {
                    $category = Category::updateOrCreate($mappedCategory);
                }
                if (isset($source['country'])) {
                    $country = Country::updateOrCreate($mappedCountry);
                }
                if (isset($source['language'])) {
                    $language = Language::updateOrCreate($mappedLanguage);
                }
                
                Source::updateOrCreate([
                    'slug' => $mappedSource['slug'],
                ], [
                    'title' => $mappedSource['title'],
                    'url' => $mappedSource['url'],
                    'provider_id' => $this->provider->id,
                    'description' => $mappedSource['description'],
                    'category_id' => $category->id,
                    'country_id' => $country->id,
                    'language_id' => $language->id,
                ]);
            }
        }
    }
}
