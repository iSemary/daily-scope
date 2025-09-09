<?php

namespace App\Services\Providers;

use App\Services\Abstractors\ProviderAbstractor;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Modules\Provider\Entities\Provider;
use Modules\Source\Entities\Source;
use Illuminate\Support\Str;
use Modules\Article\Entities\Article;
use Modules\Author\Entities\Author;
use Modules\Category\Entities\Category;
use Modules\Country\Entities\Country;
use Modules\Language\Entities\Language;

class NewsAPIAi extends ProviderAbstractor {
    private Provider $provider;
    private string $endPoint;
    private string $apiKey;
    private array $countries = ['Germany', 'United State', 'Netherlands'];

    private const NEWS_PATH = 'article/getArticles';

    public function fetch() {
        $this->fetchArticles();
    }

    public function __construct(Provider $provider) {
        $this->provider = $provider;
        $this->setApiKey($provider->api_key);
        $this->setEndPoint($provider->end_point);
    }

    protected function setApiKey(string $apiKey): void {
        try {
            $this->apiKey = Crypt::decrypt($apiKey);
        } catch (\Throwable $e) {
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

    protected function fetchArticles(): void {
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

    protected function createOrUpdateArticles(array $articles, bool $heading): void {
        if (isset($articles) && is_array($articles) && count($articles)) {
            $category = Category::updateOrCreate([
                'title' => ucfirst($articles['category']),
                'slug' => strtolower($articles['category'])
            ]);
            $country = Country::updateOrCreate([
                'name' => strtoupper($articles['country']),
                'code' => $this->getCountryCode($articles['country'])
            ]);
            foreach ($articles as $article) {
                if (isset($article['title'])) {
                    if (isset($article['lang'])) {
                        $language = Language::updateOrCreate([
                            'name' => strtoupper($article['lang']),
                            'code' => $article['lang']
                        ]);
                    }

                    $source = Source::updateOrCreate([
                        'slug' => Str::slug($article['title']),
                    ], [
                        'title' => $article['title'],
                        'provider_id' => $this->provider->id,
                        'url' => $article['uri'] ?? '/',
                        'description' => '',
                        'category_id' => $category->id,
                        'country_id' => $country->id,
                        'language_id' => $language->id,
                    ]);

                    $defaultAuthorName = $source->title . " Author";
                    $authorName = isset($article['authors'][0]['name']) ? $article['authors'][0]['name'] : $defaultAuthorName;
                    $author = Author::updateOrCreate(['name' => $authorName, 'slug' => Str::slug($authorName),], ['source_id' => $source->id]);

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
                        'description' => mb_convert_encoding(substr($article['body'], 0, 250) . "...", "UTF-8"),
                        'body' => mb_convert_encoding($article['body'] ?? '-', "UTF-8"),
                        'is_head' => $heading,
                        'reference_url' => $article['url'],
                        'image' => $article['image'],
                        'published_at' => strtotime($article['dateTime']),
                    ]);
                }
            }
        }
    }

    private function getCountryCode(string $string): string {
        $words = explode(' ', $string);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials;
    }

    protected function createOrUpdateSources(array $sources): void {
    }

    protected function fetchSources(): void {
    }

    protected function fetchTopHeadingsSources(): void {
    }

    protected function fetchTopHeadingsArticles(): void {
    }
}
