<?php

namespace Modules\Author\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Author\Entities\Author;
use Modules\Author\Interfaces\AuthorInterface;
use Modules\Article\Transformers\ArticlesCollection;

class AuthorService
{
    private AuthorInterface $authorRepository;

    public function __construct(AuthorInterface $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function list(): Collection
    {
        return $this->authorRepository->all();
    }

    public function show(int $id): ?Author
    {
        return $this->authorRepository->findById($id);
    }

    public function getArticlesBySourceAndAuthorSlug(string $sourceSlug, string $authorSlug): ArticlesCollection
    {
        $articles = $this->authorRepository->getArticlesBySourceAndAuthorSlug($sourceSlug, $authorSlug);
        return new ArticlesCollection($articles);
    }
}
