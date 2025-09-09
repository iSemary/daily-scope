<?php

namespace modules\Author\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use modules\Author\Entities\Author;

interface AuthorInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Author;
    public function findBySlug(string $slug): ?Author;
    public function getArticlesBySourceAndAuthorSlug(string $sourceSlug, string $authorSlug): LengthAwarePaginator;
}
