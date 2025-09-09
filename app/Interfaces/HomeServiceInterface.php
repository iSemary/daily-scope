<?php

namespace App\Interfaces;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface HomeServiceInterface
{
    /**
     * Get top headline articles
     * 
     * @param int|null $userId Optional user ID for personalized results
     * @return AnonymousResourceCollection
     */
    public function getTopHeadings(?int $userId = null): AnonymousResourceCollection;

    /**
     * Get preferred articles for a user
     * 
     * @param int $userId User ID
     * @return array
     */
    public function getPreferredArticles(int $userId): array;
}
