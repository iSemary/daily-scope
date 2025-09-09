<?php

namespace App\Services;

use App\Interfaces\HomeServiceInterface;
use App\Repositories\HomeRepository;
use Modules\Article\Transformers\ArticlesResource;

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
        $categoryIds = $userId ? $this->homeRepository->getUserPreferredCategoryIds($userId) : [];
        $articles = $this->homeRepository->getTopHeadings($categoryIds);

        return ArticlesResource::collection($articles);
    }

    /**
     * Get preferred articles for a user
     * 
     * @param int $userId User ID
     * @return array
     */
    public function getPreferredArticles(int $userId): array
    {
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
    }
}
