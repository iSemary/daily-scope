<?php

namespace modules\Source\Services;

use Illuminate\Database\Eloquent\Collection;
use modules\Source\Entities\Source;
use modules\Source\Interfaces\SourceInterface;
use modules\Article\Transformers\ArticlesCollection;

class SourceService
{
    private SourceInterface $sourceRepository;

    public function __construct(SourceInterface $sourceRepository)
    {
        $this->sourceRepository = $sourceRepository;
    }

    public function list(): Collection
    {
        return $this->sourceRepository->all();
    }

    public function show(int $id): ?Source
    {
        return $this->sourceRepository->findById($id);
    }

    public function getArticlesBySlug(string $slug): ArticlesCollection
    {
        $articles = $this->sourceRepository->getArticlesBySlug($slug);
        return new ArticlesCollection($articles);
    }
}
