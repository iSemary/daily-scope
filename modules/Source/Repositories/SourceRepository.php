<?php

namespace Modules\Source\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Source\Entities\Source;
use Modules\Source\Interfaces\SourceInterface;
use App\Interfaces\ItemsInterface;

class SourceRepository implements SourceInterface
{
    public function all(): Collection
    {
        return Source::select(['id', 'title', 'slug', 'url'])->orderBy("title")->get();
    }

    public function findById(int $id): ?Source
    {
        return Source::select(['id', 'title', 'slug', 'url'])->find($id);
    }

    public function findBySlug(string $slug): ?Source
    {
        return Source::select(['id', 'title', 'slug', 'url'])->where('slug', $slug)->first();
    }

    public function getArticlesBySlug(string $slug): LengthAwarePaginator
    {
        return \Modules\Article\Entities\Article::withArticleRelations()
            ->byRelatedItemSlug($slug, ItemsInterface::SOURCE, ItemsInterface::SOURCE_KEY)
            ->paginate(20);
    }
}
