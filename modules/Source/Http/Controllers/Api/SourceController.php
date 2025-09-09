<?php

namespace modules\Source\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use modules\Source\Services\SourceService;

class SourceController extends ApiController
{
    private SourceService $sourceService;

    public function __construct(SourceService $sourceService)
    {
        $this->sourceService = $sourceService;
    }

    /**
     * The index function retrieves a list of sources and returns a JSON response with the fetched sources.
     * 
     * @return JsonResponse A JSON response is being returned.
     */
    public function index(): JsonResponse
    {
        $sources = $this->sourceService->list();
        return $this->return(200, "Sources fetched successfully", ['sources' => $sources]);
    }

    /**
     * The function retrieves a paginated list of articles related to a specific source slug and returns
     * them as a JSON response.
     * 
     * @param string sourceSlug The `sourceSlug` parameter is a string that represents the slug of a
     * source. It is used to filter the articles based on the source slug.
     * 
     * @return JsonResponse A JsonResponse object is being returned.
     */
    public function articles(string $sourceSlug): JsonResponse
    {
        $articles = $this->sourceService->getArticlesBySlug($sourceSlug);
        return $this->return(200, "Source articles fetched successfully", ['articles' => $articles]);
    }
}
