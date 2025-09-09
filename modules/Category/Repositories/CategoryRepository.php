<?php

namespace Modules\Category\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Category\Entities\Category;
use Modules\Category\Interfaces\CategoryInterface;
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
        return \Modules\Article\Entities\Article::withArticleRelations()
            ->byRelatedItemSlug($slug, ItemsInterface::CATEGORY, ItemsInterface::CATEGORY_KEY)
            ->paginate(20);
    }
}
