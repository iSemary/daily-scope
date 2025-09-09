<?php

namespace Tests\Unit;

use Tests\TestCase;
use Modules\Article\Services\ArticleService;
use Modules\Article\Interfaces\ArticleInterface;
use Modules\Article\Entities\Article;
use Modules\Article\Transformers\ArticleResource;
use Modules\Article\Transformers\ArticlesCollection;
use Mockery;

class ArticleServiceTest extends TestCase
{
    protected ArticleService $articleService;
    protected $mockArticleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockArticleRepository = Mockery::mock(ArticleInterface::class);
        $this->articleService = new ArticleService($this->mockArticleRepository);
    }

    public function test_show_article_success(): void
    {
        $article = new Article();
        $article->id = 1;
        $article->title = 'Test Article';
        $article->slug = 'test-article';
        $article->category_id = 1;
        $article->source_id = 1;

        $relatedArticles = new \Illuminate\Database\Eloquent\Collection([$article]);

        $this->mockArticleRepository
            ->shouldReceive('findBySourceAndArticleSlug')
            ->with('test-source', 'test-article')
            ->once()
            ->andReturn($article);

        $this->mockArticleRepository
            ->shouldReceive('getRelatedArticles')
            ->with(1, 1)
            ->once()
            ->andReturn($relatedArticles);

        $result = $this->articleService->show('test-source', 'test-article');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('article', $result);
        $this->assertArrayHasKey('related_articles', $result);
        $this->assertInstanceOf(ArticleResource::class, $result['article']);
        $this->assertInstanceOf(ArticlesCollection::class, $result['related_articles']);
    }

    public function test_show_article_not_found(): void
    {
        $this->mockArticleRepository
            ->shouldReceive('findBySourceAndArticleSlug')
            ->with('non-existent-source', 'non-existent-article')
            ->once()
            ->andReturn(null);

        $result = $this->articleService->show('non-existent-source', 'non-existent-article');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('article', $result);
        $this->assertArrayHasKey('related_articles', $result);
        $this->assertNull($result['article']);
        $this->assertNull($result['related_articles']);
    }

    public function test_search_articles_with_all_parameters(): void
    {
        $searchData = [
            'keyword' => 'test',
            'category_id' => 1,
            'source_id' => 1,
            'date_order' => 'desc',
        ];

        $articles = new \Illuminate\Pagination\LengthAwarePaginator(
            [new Article()], 1, 10, 1, []
        );

        $this->mockArticleRepository
            ->shouldReceive('search')
            ->with('test', 1, 1, 'desc')
            ->once()
            ->andReturn($articles);

        $result = $this->articleService->search($searchData);

        $this->assertInstanceOf(ArticlesCollection::class, $result);
    }

    public function test_search_articles_with_defaults(): void
    {
        $searchData = [
            'keyword' => 'test',
        ];

        $articles = new \Illuminate\Pagination\LengthAwarePaginator(
            [new Article()], 1, 10, 1, []
        );

        $this->mockArticleRepository
            ->shouldReceive('search')
            ->with('test', null, null, 'desc')
            ->once()
            ->andReturn($articles);

        $result = $this->articleService->search($searchData);

        $this->assertInstanceOf(ArticlesCollection::class, $result);
    }

    public function test_search_deeply(): void
    {
        $searchData = [
            'keyword' => 'test',
        ];

        $articles = new \Illuminate\Pagination\LengthAwarePaginator(
            [new Article()], 1, 10, 1, []
        );

        $this->mockArticleRepository
            ->shouldReceive('searchDeeply')
            ->with('test')
            ->once()
            ->andReturn($articles);

        $result = $this->articleService->searchDeeply($searchData);

        $this->assertInstanceOf(ArticlesCollection::class, $result);
    }

    public function test_get_today_articles(): void
    {
        $articles = new \Illuminate\Pagination\LengthAwarePaginator(
            [new Article()], 1, 10, 1, []
        );

        $this->mockArticleRepository
            ->shouldReceive('getTodayArticles')
            ->once()
            ->andReturn($articles);

        $result = $this->articleService->getTodayArticles();

        $this->assertInstanceOf(ArticlesCollection::class, $result);
    }

    public function test_get_by_source_and_author_slug(): void
    {
        $articles = new \Illuminate\Pagination\LengthAwarePaginator(
            [new Article()], 1, 10, 1, []
        );

        $this->mockArticleRepository
            ->shouldReceive('getBySourceAndAuthorSlug')
            ->with('test-source', 'test-author')
            ->once()
            ->andReturn($articles);

        $result = $this->articleService->getBySourceAndAuthorSlug('test-source', 'test-author');

        $this->assertInstanceOf(ArticlesCollection::class, $result);
    }

    public function test_get_by_related_item_slug(): void
    {
        $articles = new \Illuminate\Pagination\LengthAwarePaginator(
            [new Article()], 1, 10, 1, []
        );

        $this->mockArticleRepository
            ->shouldReceive('getByRelatedItemSlug')
            ->with('test-slug', 1, 'test-key')
            ->once()
            ->andReturn($articles);

        $result = $this->articleService->getByRelatedItemSlug('test-slug', 1, 'test-key');

        $this->assertInstanceOf(ArticlesCollection::class, $result);
    }

    public function test_article_service_constructor(): void
    {
        $this->assertInstanceOf(ArticleService::class, $this->articleService);
    }

    public function test_article_service_has_required_methods(): void
    {
        $this->assertTrue(method_exists($this->articleService, 'show'));
        $this->assertTrue(method_exists($this->articleService, 'search'));
        $this->assertTrue(method_exists($this->articleService, 'searchDeeply'));
        $this->assertTrue(method_exists($this->articleService, 'getTodayArticles'));
        $this->assertTrue(method_exists($this->articleService, 'getBySourceAndAuthorSlug'));
        $this->assertTrue(method_exists($this->articleService, 'getByRelatedItemSlug'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}