<?php

namespace modules\Author\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use modules\Author\Entities\Author;
use modules\Author\Interfaces\AuthorInterface;

class AuthorRepository implements AuthorInterface
{
    public function all(): Collection
    {
        return Author::select(['id', 'name', 'slug'])->orderBy("name")->get();
    }

    public function findById(int $id): ?Author
    {
        return Author::select(['id', 'name', 'slug'])->find($id);
    }

    public function findBySlug(string $slug): ?Author
    {
        return Author::select(['id', 'name', 'slug'])->where('slug', $slug)->first();
    }

    public function getArticlesBySourceAndAuthorSlug(string $sourceSlug, string $authorSlug): LengthAwarePaginator
    {
        return \modules\Article\Entities\Article::withArticleRelations()
            ->bySourceAndAuthorSlug($sourceSlug, $authorSlug)
            ->paginate(20);
    }
}
