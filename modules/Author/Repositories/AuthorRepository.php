<?php

namespace Modules\Author\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Author\Entities\Author;
use Modules\Author\Interfaces\AuthorInterface;

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
        return \Modules\Article\Entities\Article::withArticleRelations()
            ->bySourceAndAuthorSlug($sourceSlug, $authorSlug)
            ->paginate(20);
    }
}
