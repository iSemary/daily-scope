<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Article\Entities\Article;
use Modules\User\Entities\UserInterest;
use Modules\User\Interfaces\UserInterestTypes;
use Illuminate\Support\Facades\Cache;

class HomeRepository
{
    /**
     * Get top headline articles with optional user filtering
     * 
     * @param array $categoryIds User's preferred category IDs
     * @param int $limit Number of articles to return
     * @return Collection
     */
    public function getTopHeadings(array $categoryIds = [], int $limit = 5): Collection
    {
        return Article::withArticleRelations()
            ->where("is_head", 1)
            ->when($categoryIds, function ($query) use ($categoryIds) {
                return $query->whereHas('source', function ($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds);
                });
            })
            ->orderByDesc("published_at")
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's preferred source articles
     * 
     * @param int $userId User ID
     * @param int $limit Number of articles to return
     * @return LengthAwarePaginator
     */
    public function getPreferredSourceArticles(int $userId, int $limit = 8): LengthAwarePaginator
    {
        $sourceIds = UserInterest::getItemIds($userId, UserInterestTypes::SOURCE);
        
        return Article::with(['language', 'country', 'source', 'author', 'category', 'provider'])
            ->whereIn("articles.source_id", $sourceIds)
            ->orderByDesc("published_at")
            ->limit($limit)
            ->paginate();
    }

    /**
     * Get user's preferred category articles
     * 
     * @param int $userId User ID
     * @param int $limit Number of articles to return
     * @return LengthAwarePaginator
     */
    public function getPreferredCategoryArticles(int $userId, int $limit = 9): LengthAwarePaginator
    {
        $categoryIds = UserInterest::getItemIds($userId, UserInterestTypes::CATEGORY);
        
        return Article::withArticleRelations()
            ->whereHas('source', function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->orderByDesc("published_at")
            ->limit($limit)
            ->paginate();
    }

    /**
     * Get user's preferred author articles
     * 
     * @param int $userId User ID
     * @param int $limit Number of articles to return
     * @return LengthAwarePaginator
     */
    public function getPreferredAuthorArticles(int $userId, int $limit = 9): LengthAwarePaginator
    {
        $authorIds = UserInterest::getItemIds($userId, UserInterestTypes::AUTHOR);
        
        return Article::withArticleRelations()
            ->whereIn("articles.author_id", $authorIds)
            ->orderByDesc("published_at")
            ->limit($limit)
            ->paginate();
    }

    /**
     * Get user's preferred category IDs
     * 
     * @param int $userId User ID
     * @return array
     */
    public function getUserPreferredCategoryIds(int $userId): array
    {
        // Get cache settings from config
        $cacheSettings = config('cache_settings.home.user_categories');
        $keyPrefix = $cacheSettings['key_prefix'];
        $cacheTtl = $cacheSettings['ttl'];
        
        $cacheKey = "{$keyPrefix}_{$userId}";
        
        return Cache::remember($cacheKey, $cacheTtl, function () use ($userId) {
            return UserInterest::getItemIds($userId, UserInterestTypes::CATEGORY);
        });
    }
}
