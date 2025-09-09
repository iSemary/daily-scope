<?php

namespace App\Services\Mappers\Providers;

use App\Services\Mappers\ArticleMapper;
use App\Services\Mappers\SourceMapper;
use App\Services\Mappers\AuthorMapper;
use App\Services\Mappers\CategoryMapper;
use App\Services\Mappers\CountryMapper;
use App\Services\Mappers\LanguageMapper;

class NewsAPIAiMapper
{
    private ArticleMapper $articleMapper;
    private SourceMapper $sourceMapper;
    private AuthorMapper $authorMapper;
    private CategoryMapper $categoryMapper;
    private CountryMapper $countryMapper;
    private LanguageMapper $languageMapper;

    public function __construct()
    {
        $this->articleMapper = new ArticleMapper();
        $this->sourceMapper = new SourceMapper();
        $this->authorMapper = new AuthorMapper();
        $this->categoryMapper = new CategoryMapper();
        $this->countryMapper = new CountryMapper();
        $this->languageMapper = new LanguageMapper();
    }

    public function mapArticle(array $article, bool $isHeading = false): array
    {
        $mappedArticle = $this->articleMapper->map($article);
        $mappedArticle['is_head'] = $isHeading;
        
        return $mappedArticle;
    }

    public function mapSource(array $article): array
    {
        return $this->sourceMapper->map($article);
    }

    public function mapAuthor(array $article, $source): array
    {
        $defaultAuthorName = $source->title . " Author";
        $authorData = [
            'name' => $article['authors'][0]['name'] ?? null,
            'author' => $article['authors'][0]['name'] ?? null,
        ];
        
        return $this->authorMapper->map($authorData, $defaultAuthorName);
    }

    public function mapCategory(array $articles): array
    {
        return $this->categoryMapper->map($articles);
    }

    public function mapCountry(array $articles): array
    {
        return $this->countryMapper->map($articles);
    }

    public function mapLanguage(array $article): array
    {
        return $this->languageMapper->map($article);
    }
}
