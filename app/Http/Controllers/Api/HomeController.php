<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Interfaces\HomeServiceInterface;
use Illuminate\Http\JsonResponse;

class HomeController extends ApiController
{
    protected HomeServiceInterface $homeService;

    public function __construct(HomeServiceInterface $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * Get top headline articles
     * 
     * @return JsonResponse
     */
    public function topHeadings(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $userId = $user ? $user->id : null;
        
        $topHeadings = $this->homeService->getTopHeadings($userId);
        
        return $this->return(200, "Top headings fetched successfully", [
            'headings' => $topHeadings
        ]);
    }

    /**
     * Get preferred articles for authenticated user
     * 
     * @return JsonResponse
     */
    public function preferredArticles(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        
        $preferredArticles = $this->homeService->getPreferredArticles($user->id);
        
        return $this->return(200, "Preferred articles fetched successfully", [
            'articles' => $preferredArticles
        ]);
    }
}
