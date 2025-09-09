<?php

namespace App\Services\Mappers\Providers;

use App\Services\Mappers\ArticleMapper;
use App\Services\Mappers\SourceMapper;
use App\Services\Mappers\AuthorMapper;
use App\Services\Mappers\CategoryMapper;
use App\Services\Mappers\CountryMapper;
use App\Services\Mappers\LanguageMapper;

class NewsAPIMapper
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

    public function mapArticle(array $article, $source, bool $isHeading = false): array
    {
        $mappedArticle = $this->articleMapper->map($article);
        $mappedArticle['is_head'] = $isHeading;
        
        return $mappedArticle;
    }

    public function mapSource(array $source): array
    {
        return $this->sourceMapper->map($source);
    }

    public function mapAuthor(array $article, $source): array
    {
        $defaultAuthorName = $source->title . " Author";
        return $this->authorMapper->map($article, $defaultAuthorName);
    }

    public function mapCategory(array $source): array
    {
        return $this->categoryMapper->map($source);
    }

    public function mapCountry(array $source): array
    {
        return $this->countryMapper->map($source);
    }

    public function mapLanguage(array $source): array
    {
        return $this->languageMapper->map($source);
    }
}
