<?php

namespace App\Services\Providers;

use App\Services\Abstractors\ProviderAbstractor;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use modules\Provider\Entities\Provider;
use modules\Source\Entities\Source;
use Illuminate\Support\Str;
use modules\Article\Entities\Article;
use modules\Author\Entities\Author;
use modules\Category\Entities\Category;
use modules\Country\Entities\Country;
use modules\Language\Entities\Language;

class NewsAPI extends ProviderAbstractor {
    private Provider $provider;
    private string $endPoint;
    private string $apiKey;
    private array $countries = ['de', 'us', 'nl'];

    private const SOURCES_PATH = '/sources';
    private const EVERYTHING_PATH = '/everything';
    private const TOP_HEADLINES_SOURCES_PATH = '/top-headlines/sources';
    private const TOP_HEADLINES_PATH = '/top-headlines';

    public function __construct(Provider $provider) {
        $this->provider = $provider;
        $this->setApiKey($provider->api_key);
        $this->setEndPoint($provider->end_point);
    }

    public function fetch() {
        $this->fetchSources();
        $this->fetchTopHeadingsSources();
        $this->fetchArticles();
        $this->fetchTopHeadingsArticles();
    }

    protected function setApiKey(string $apiKey): void {
        try {
            $this->apiKey = Crypt::decrypt($apiKey);
        } catch (\Throwable $e) {
            // Fallback for legacy plaintext keys
            $this->apiKey = $apiKey;
        }
    }

    protected function getApiKey(): string {
        return $this->apiKey;
    }

    protected function setEndPoint(string $endPoint): void {
        $this->endPoint = $endPoint;
    }

    protected function getEndPoint(): string {
        return $this->endPoint;
    }

    protected function fetchSources(): void {
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

    protected function fetchArticles(): void {
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

    protected function fetchTopHeadingsSources(): void {
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

    protected function fetchTopHeadingsArticles(): void {
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

    protected function createOrUpdateArticles(array $articles, bool $heading): void {
        if (isset($articles['articles']) && is_array($articles['articles']) && count($articles['articles'])) {
            foreach ($articles['articles'] as $article) {
                $source = isset($articles['source']) ? $articles['source'] : Source::where("slug", $article['source']['id'])->where("provider_id", $this->provider->id)->first();
                if (isset($article['content'])) {
                    $defaultAuthorName = $source->title . " Author";
                    $author = Author::updateOrCreate(
                        [
                            'name' => $article['author'] && !empty($article['author']) ? $article['author'] : $defaultAuthorName,
                            'slug' => $article['author'] && !empty($article['author']) ? Str::slug($article['author']) : Str::slug($defaultAuthorName),
                        ],
                        [
                            'source_id' => $source->id
                        ]
                    );

                    $description = $article['description'] ?? "";

                    Article::updateOrCreate([
                        'slug' => Str::slug(substr($article['title'], 0, 100))
                    ], [
                        'title' => $article['title'],
                        'author_id' => $author->id,
                        'source_id' => $source->id,
                        'provider_id' => $this->provider->id,
                        'category_id' => $source->category_id,
                        'language_id' => $source->language_id,
                        'country_id' => $source->country_id,
                        'description' => substr($description, 0, 1000),
                        'body' => $article['content'] ?? '-',
                        'is_head' => $heading,
                        'reference_url' => $article['url'],
                        'image' => $article['urlToImage'],
                        'published_at' => strtotime($article['publishedAt']),
                    ]);
                }
            }
        }
    }

    protected function createOrUpdateSources(array $sources): void {
        if (isset($sources) && is_array($sources) && count($sources)) {
            foreach ($sources as $source) {
                if (isset($source['category'])) {
                    $category = Category::updateOrCreate([
                        'title' => ucfirst($source['category']),
                        'slug' => $source['category']
                    ]);
                }
                if (isset($source['country'])) {
                    $country = Country::updateOrCreate([
                        'name' => strtoupper($source['country']),
                        'code' => $source['country']
                    ]);
                }
                if (isset($source['language'])) {
                    $language = Language::updateOrCreate([
                        'name' => strtoupper($source['language']),
                        'code' => $source['language']
                    ]);
                }
                Source::updateOrCreate([
                    'slug' => Str::slug($source['name']),
                ], [
                    'title' => $source['name'],
                    'url' => $source['url'] ?? '/',
                    'provider_id' => $this->provider->id,
                    'description' => $source['description'],
                    'category_id' => $category->id,
                    'country_id' => $country->id,
                    'language_id' => $language->id,
                ]);
            }
        }
    }
}
