<?php

namespace modules\Article\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use modules\Article\Entities\Article;
use modules\Article\Interfaces\ArticleInterface;
use modules\Article\Transformers\ArticleResource;
use modules\Article\Transformers\ArticlesCollection;
use App\Interfaces\ItemsInterface;

class ArticleService
{
    private ArticleInterface $articleRepository;

    public function __construct(ArticleInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function show(string $sourceSlug, string $articleSlug): array
    {
        $article = $this->articleRepository->findBySourceAndArticleSlug($sourceSlug, $articleSlug);
        
        if (!$article) {
            return ['article' => null, 'related_articles' => null];
        }

        $articleResource = new ArticleResource($article);
        $relatedArticles = $this->articleRepository->getRelatedArticles(
            $article->category_id,
            $article->source_id
        );
        $relatedArticlesCollection = new ArticlesCollection($relatedArticles);

        return [
            'article' => $articleResource,
            'related_articles' => $relatedArticlesCollection
        ];
    }

    public function search(array $searchData): ArticlesCollection
    {
        $keyword = $searchData['keyword'];
        $categoryId = $searchData['category_id'] ?? null;
        $sourceId = $searchData['source_id'] ?? null;
        $dateOrder = $searchData['date_order'] ?? 'desc';

        $articles = $this->articleRepository->search($keyword, $categoryId, $sourceId, $dateOrder);
        return new ArticlesCollection($articles);
    }

    public function searchDeeply(array $searchData): ArticlesCollection
    {
        $keyword = $searchData['keyword'];
        $articles = $this->articleRepository->searchDeeply($keyword);
        return new ArticlesCollection($articles);
    }

    public function getTodayArticles(): ArticlesCollection
    {
        $articles = $this->articleRepository->getTodayArticles();
        return new ArticlesCollection($articles);
    }

    public function getBySourceAndAuthorSlug(string $sourceSlug, string $authorSlug): ArticlesCollection
    {
        $articles = $this->articleRepository->getBySourceAndAuthorSlug($sourceSlug, $authorSlug);
        return new ArticlesCollection($articles);
    }

    public function getByRelatedItemSlug(string $slug, int $itemType, string $itemKey): ArticlesCollection
    {
        $articles = $this->articleRepository->getByRelatedItemSlug($slug, $itemType, $itemKey);
        return new ArticlesCollection($articles);
    }
}
