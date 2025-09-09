<?php

namespace modules\Category\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use modules\Category\Entities\Category;
use modules\Category\Interfaces\CategoryInterface;
use App\Interfaces\ItemsInterface;

class CategoryRepository implements CategoryInterface
{
    public function all(): Collection
    {
        return Category::select(['categories.id', 'categories.title', 'categories.slug'])
            ->orderBy('categories.title')
            ->get();
    }

    public function findById(int $id): ?Category
    {
        return Category::select(['categories.id', 'categories.title', 'categories.slug'])->find($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::select(['categories.id', 'categories.title', 'categories.slug'])
            ->where("slug", $slug)
            ->first();
    }

    public function getArticlesBySlug(string $slug): LengthAwarePaginator
    {
        return \modules\Article\Entities\Article::withArticleRelations()
            ->byRelatedItemSlug($slug, ItemsInterface::CATEGORY, ItemsInterface::CATEGORY_KEY)
            ->paginate(20);
    }
}
