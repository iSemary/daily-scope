<?php

namespace Modules\Article\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Modules\Article\Http\Requests\SearchRequest;
use Modules\Article\Services\ArticleService;

class ArticleController extends ApiController
{
    private ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function show(string $sourceSlug, string $articleSlug): JsonResponse
    {
        $result = $this->articleService->show($sourceSlug, $articleSlug);
        
        if (!$result['article']) {
            return $this->return(404, "Article not found");
        }
        
        return $this->return(200, "Article fetched successfully", [
            'article' => $result['article'],
            'related_articles' => $result['related_articles']
        ]);
    }

    public function find(SearchRequest $searchRequest): JsonResponse
    {
        $searchData = $searchRequest->validated();
        $articles = $this->articleService->search($searchData);
        
        return $this->return(200, "Articles fetched successfully", ['articles' => $articles]);
    }

    public function findDeeply(SearchRequest $searchRequest): JsonResponse
    {
        $searchData = $searchRequest->validated();
        $articles = $this->articleService->searchDeeply($searchData);
        
        return $this->return(200, "Articles fetched successfully", ['articles' => $articles]);
    }

    public function todayArticles(): JsonResponse
    {
        $articles = $this->articleService->getTodayArticles();
        
        return $this->return(200, "Today's articles fetched successfully", ['articles' => $articles]);
    }
}
