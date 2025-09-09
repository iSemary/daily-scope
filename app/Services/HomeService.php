<?php

namespace App\Services;

use App\Interfaces\HomeServiceInterface;
use App\Repositories\HomeRepository;
use Modules\Article\Transformers\ArticlesResource;
use Illuminate\Support\Facades\Cache;

class HomeService implements HomeServiceInterface
{
    protected HomeRepository $homeRepository;

    public function __construct(HomeRepository $homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    /**
     * Get top headline articles
     * 
     * @param int|null $userId Optional user ID for personalized results
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getTopHeadings(?int $userId = null): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        // Get cache settings from config
        $cacheSettings = config('cache_settings.home.top_headings');
        $keyPrefix = $cacheSettings['key_prefix'];
        $cacheTtl = $cacheSettings['ttl'];
        
        // Create cache key based on user ID for personalized caching
        $cacheKey = $userId ? "{$keyPrefix}_user_{$userId}" : "{$keyPrefix}_guest";
        
        return Cache::remember($cacheKey, $cacheTtl, function () use ($userId) {
            $categoryIds = $userId ? $this->homeRepository->getUserPreferredCategoryIds($userId) : [];
            $articles = $this->homeRepository->getTopHeadings($categoryIds);
            
            return ArticlesResource::collection($articles);
        });
    }

    /**
     * Get preferred articles for a user
     * 
     * @param int $userId User ID
     * @return array
     */
    public function getPreferredArticles(int $userId): array
    {
        // Get cache settings from config
        $cacheSettings = config('cache_settings.home.preferred_articles');
        $keyPrefix = $cacheSettings['key_prefix'];
        $cacheTtl = $cacheSettings['ttl'];
        
        // Create cache key for user's preferred articles
        $cacheKey = "{$keyPrefix}_user_{$userId}";
        
        return Cache::remember($cacheKey, $cacheTtl, function () use ($userId) {
            return [
                'sources' => new \Modules\Article\Transformers\ArticlesCollection(
                    $this->homeRepository->getPreferredSourceArticles($userId)
                ),
                'authors' => new \Modules\Article\Transformers\ArticlesCollection(
                    $this->homeRepository->getPreferredAuthorArticles($userId)
                ),
                'categories' => new \Modules\Article\Transformers\ArticlesCollection(
                    $this->homeRepository->getPreferredCategoryArticles($userId)
                ),
            ];
        });
    }
}
