<?php

namespace Modules\Category\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Category\Entities\Category;

interface CategoryInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Category;
    public function findBySlug(string $slug): ?Category;
    public function getArticlesBySlug(string $slug): LengthAwarePaginator;
}
