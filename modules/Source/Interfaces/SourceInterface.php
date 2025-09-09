<?php

namespace Modules\Source\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Source\Entities\Source;

interface SourceInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Source;
    public function findBySlug(string $slug): ?Source;
    public function getArticlesBySlug(string $slug): LengthAwarePaginator;
}
