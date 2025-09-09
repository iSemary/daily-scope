<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Modules\Article\Entities\Article;
use Modules\Article\Transformers\ArticlesResource;
use Modules\Category\Entities\Category;
use Modules\User\Entities\UserInterest;
use Modules\User\Interfaces\UserInterestTypes;

class HomeController extends ApiController {
    /**
     * The function "topHeadings" fetches the top headings and returns them as a JSON response.
     * 
     * @return JsonResponse a JsonResponse object.
     */
    public function topHeadings(): JsonResponse {
        $topHeadings = $this->getTopHeadings();
        $topHeadings = ArticlesResource::collection($topHeadings);
        return $this->return(200, "Top headings fetched successfully", ['headings' => $topHeadings]);
    }

    /**
     * The function "preferredArticles" fetches preferred articles and returns a JSON response with the
     * fetched articles.
     * 
     * @return JsonResponse A JsonResponse object is being returned.
     */
    public function preferredArticles(): JsonResponse {
        $preferredArticles = $this->getPreferredArticles();
        return $this->return(200, "Preferred articles fetched successfully", ['articles' => $preferredArticles]);
    }

    /**
     * The function `getPreferredArticles()` retrieves preferred articles for a user based on their
     * sources, authors, and categories.
     * 
     * @return array an array called ``. This array contains three keys: 'sources',
     * 'authors', and 'categories'. The values associated with each key are the results of different
     * methods that retrieve preferred articles based on the authenticated user's ID.
     */
    private function getPreferredArticles(): array {
        $user = $this->getAuthenticatedUser();
        $combinedArticles = [
            'sources' => Article::getPreferredSourceArticles($user->id),
            'authors' => Article::getPreferredAuthorArticles($user->id),
            'categories' => Article::getPreferredCategoryArticles($user->id),
        ];
        return $combinedArticles;
    }

    /**
     * The function `getTopHeadings()` retrieves the top 5 articles that are marked as headlines, ordered
     * by their published date, and filtered based on the authenticated user's interests.
     * If auth then get from the prefer categories and sources
     * 
     * 
     * @return Collection The function `getTopHeadings()` returns a collection of articles that are marked
     * as "head" (is_head = 1) and are ordered by their published date in descending order. The number of
     * articles returned is limited to 5. The articles are filtered based on the user's interests, if the
     * user is authenticated.
     */
    private function getTopHeadings(): Collection {
        $user = $this->getAuthenticatedUser();
        $categoryIds = $user ? UserInterest::getItemIds($user->id, UserInterestTypes::CATEGORY) : [];
        return Article::withArticleRelations()->where("is_head", 1)
            ->when($categoryIds, function ($query) use ($categoryIds) {
                return $query->whereIn('category_id', $categoryIds);
            })->orderByDesc("published_at")->limit(5)->get();
    }
}
