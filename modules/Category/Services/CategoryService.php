<?php

namespace modules\Category\Services;

use Illuminate\Database\Eloquent\Collection;
use modules\Category\Entities\Category;
use modules\Category\Interfaces\CategoryInterface;
use modules\Article\Transformers\ArticlesCollection;

class CategoryService
{
    private CategoryInterface $categoryRepository;

    public function __construct(CategoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function list(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function show(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    public function getArticlesBySlug(string $slug): array
    {
        $category = $this->categoryRepository->findBySlug($slug);
        
        if (!$category) {
            return ['category' => null, 'articles' => null];
        }

        $articles = $this->categoryRepository->getArticlesBySlug($slug);
        $articlesCollection = new ArticlesCollection($articles);

        return [
            'category' => $category,
            'articles' => $articlesCollection
        ];
    }
}
