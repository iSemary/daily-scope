<?php

namespace Modules\Article\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Article\Entities\Article;
use Modules\Article\Interfaces\ArticleInterface;
use App\Interfaces\ItemsInterface;

class ArticleRepository implements ArticleInterface
{
    public function all(): Collection
    {
        return Article::withArticleRelations()->get();
    }

    public function findById(int $id): ?Article
    {
        return Article::withArticleRelations()->find($id);
    }

    public function findBySourceAndArticleSlug(string $sourceSlug, string $articleSlug): ?Article
    {
        return Article::bySourceAndArticleSlug($sourceSlug, $articleSlug)
            ->withArticleRelations()
            ->first();
    }

    public function search(string $keyword, ?int $categoryId = null, ?int $sourceId = null, string $dateOrder = 'desc'): LengthAwarePaginator
    {
        $query = Article::withArticleRelations()
            ->where("title", "like", "%" . $keyword . "%")
            ->orWhere("description", "like", "%" . $keyword . "%");

        if ($categoryId) {
            $query->where("articles.category_id", $categoryId);
        }

        if ($sourceId) {
            $query->where("articles.source_id", $sourceId);
        }

        return $query->orderBy("published_at", $dateOrder)->paginate(20);
    }

    public function searchDeeply(string $keyword): LengthAwarePaginator
    {
        return Article::withArticleRelations()
            ->where("title", "like", "%" . $keyword . "%")
            ->orWhere("description", "like", "%" . $keyword . "%")
            ->orWhere("body", "like", "%" . $keyword . "%")
            ->paginate(20);
    }

    public function getTodayArticles(): LengthAwarePaginator
    {
        $today = Carbon::today();
        return Article::withArticleRelations()
            ->whereDate(
                DB::raw('FROM_UNIXTIME(published_at)'),
                '=',
                $today->toDateString()
            )->paginate(20);
    }

    public function getBySourceAndAuthorSlug(string $sourceSlug, string $authorSlug): LengthAwarePaginator
    {
        return Article::withArticleRelations()
            ->bySourceAndAuthorSlug($sourceSlug, $authorSlug)
            ->paginate(20);
    }

    public function getByRelatedItemSlug(string $slug, int $itemType, string $itemKey): LengthAwarePaginator
    {
        return Article::withArticleRelations()
            ->byRelatedItemSlug($slug, $itemType, $itemKey)
            ->paginate(20);
    }

    public function getRelatedArticles(int $categoryId, int $sourceId, int $limit = 8): Collection
    {
        return Article::withArticleRelations()
            ->where('category_id', $categoryId)
            ->where('source_id', $sourceId)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
