<?php

namespace modules\Category\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use modules\Category\Services\CategoryService;

class CategoryController extends ApiController
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * The index function retrieves categories from the database and returns them in a JSON response.
     * 
     * @return JsonResponse a JsonResponse with a status code of 200, a message of "Categories fetched
     * successfully", and an array of categories.
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->list();
        return $this->return(200, "Categories fetched successfully", ['categories' => $categories]);
    }

    /**
     * The function retrieves articles belonging to a specific category and returns them in a JSON
     * response, while also recording the user's view if they are authenticated.
     * 
     * @param string categorySlug The parameter "categorySlug" is a string that represents the slug of a
     * category. A slug is a URL-friendly version of a string, typically used in URLs to identify a
     * resource. In this case, the categorySlug is used to find a category in the database based on its
     * slug value.
     * 
     * @return JsonResponse a JsonResponse.
     */
    public function articles(string $categorySlug): JsonResponse
    {
        $result = $this->categoryService->getArticlesBySlug($categorySlug);
        
        if (!$result['category']) {
            return $this->return(400, "Category not found");
        }
        
        return $this->return(200, "Category articles fetched successfully", [
            'articles' => $result['articles']
        ]);
    }
}
