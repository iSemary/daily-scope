<?php

namespace modules\Article\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Interfaces\ItemsInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use modules\Article\Entities\Article;
use modules\Article\Http\Requests\SearchRequest;
use modules\Article\Transformers\ArticleResource;
use modules\Article\Transformers\ArticlesCollection;

class ArticleController extends ApiController {

    public function show(string $sourceSlug, string $articleSlug): JsonResponse {
        $article = Article::bySourceAndArticleSlug($sourceSlug, $articleSlug)->withArticleRelations()->first();
        if (!$article) {
            return $this->return(404, "Article not found");
        }
        
        $article = new ArticleResource($article);
        $relatedArticles = Article::withArticleRelations()
            ->where('category_id', $article->category_id)
            ->where('source_id', $article->source_id)
            ->inRandomOrder()
            ->limit(8)
            ->get();
        $relatedArticles = new ArticlesCollection($relatedArticles);
        
        return $this->return(200, "Article fetched successfully", ['article' => $article, 'related_articles' => $relatedArticles]);
    }

    public function find(SearchRequest $searchRequest): JsonResponse {
        $searchRequest = $searchRequest->validated();
        $keyword = $searchRequest['keyword'];
        $categoryId = $searchRequest['category_id'] ?? "";
        $sourceId = $searchRequest['source_id'] ?? "";

        $articles = Article::withArticleRelations()->where("title", "like", "%" . $keyword . "%")
            ->orWhere("description", "like", "%" . $keyword . "%")
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where("articles.category_id", $categoryId);
            })
            ->when($sourceId, function ($query) use ($sourceId) {
                $query->where("articles.source_id", $sourceId);
            })
            ->orderBy("published_at", $searchRequest['date_order'])
            ->paginate(20);
        $articles = new ArticlesCollection($articles);

        return $this->return(200, "Articles fetched successfully", ['articles' => $articles]);
    }

    public function findDeeply(SearchRequest $searchRequest): JsonResponse {
        $searchRequest = $searchRequest->validated();
        $keyword = $searchRequest['keyword'];

        $articles = Article::withArticleRelations()->where("title", "like", "%" . $keyword . "%")
            ->orWhere("description", "like", "%" . $keyword . "%")
            ->orWhere("body", "like", "%" . $keyword . "%")
            ->paginate(20);
        $articles = new ArticlesCollection($articles);
        
        return $this->return(200, "Articles fetched successfully", ['articles' => $articles]);
    }

    public function todayArticles(): JsonResponse {
        $today = Carbon::today();
        $articles = Article::withArticleRelations()->whereDate(
            DB::raw('FROM_UNIXTIME(published_at)'),
            '=',
            $today->toDateString()
        )->paginate(20);
        $articles = new ArticlesCollection($articles);
        
        return $this->return(200, "Today's articles fetched successfully", ['articles' => $articles]);
    }
}
