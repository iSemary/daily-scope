<?php

namespace Modules\Article\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Article\Entities\Article;

interface ArticleInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Article;
    public function findBySourceAndArticleSlug(string $sourceSlug, string $articleSlug): ?Article;
    public function search(string $keyword, ?int $categoryId = null, ?int $sourceId = null, string $dateOrder = 'desc'): LengthAwarePaginator;
    public function searchDeeply(string $keyword): LengthAwarePaginator;
    public function getTodayArticles(): LengthAwarePaginator;
    public function getBySourceAndAuthorSlug(string $sourceSlug, string $authorSlug): LengthAwarePaginator;
    public function getByRelatedItemSlug(string $slug, int $itemType, string $itemKey): LengthAwarePaginator;
    public function getRelatedArticles(int $categoryId, int $sourceId, int $limit = 8): Collection;
}
