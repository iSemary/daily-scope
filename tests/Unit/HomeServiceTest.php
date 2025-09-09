<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\HomeService;
use App\Repositories\HomeRepository;
use Modules\Article\Transformers\ArticlesResource;
use Illuminate\Support\Facades\Cache;
use Mockery;

class HomeServiceTest extends TestCase
{
    protected HomeService $homeService;
    protected $mockHomeRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockHomeRepository = Mockery::mock(HomeRepository::class);
        $this->homeService = new HomeService($this->mockHomeRepository);
    }

    public function test_home_service_constructor(): void
    {
        $this->assertInstanceOf(HomeService::class, $this->homeService);
    }

    public function test_home_service_has_required_methods(): void
    {
        $this->assertTrue(method_exists($this->homeService, 'getTopHeadings'));
        $this->assertTrue(method_exists($this->homeService, 'getPreferredArticles'));
    }

    public function test_get_top_headings_guest_user(): void
    {
        $articles = new \Illuminate\Database\Eloquent\Collection([new \Modules\Article\Entities\Article()]);
        $expectedResult = ArticlesResource::collection($articles);

        Cache::shouldReceive('remember')
            ->once()
            ->with(Mockery::type('string'), Mockery::any(), Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        // For guest users, getUserPreferredCategoryIds is not called
        $this->mockHomeRepository
            ->shouldReceive('getTopHeadings')
            ->with([])
            ->once()
            ->andReturn($articles);

        $result = $this->homeService->getTopHeadings(null);

        $this->assertInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class, $result);
    }

    public function test_get_top_headings_authenticated_user(): void
    {
        $userId = 1;
        $categoryIds = [1, 2, 3];
        $articles = new \Illuminate\Database\Eloquent\Collection([new \Modules\Article\Entities\Article()]);
        $expectedResult = ArticlesResource::collection($articles);

        Cache::shouldReceive('remember')
            ->once()
            ->with(Mockery::type('string'), Mockery::any(), Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->mockHomeRepository
            ->shouldReceive('getUserPreferredCategoryIds')
            ->with($userId)
            ->once()
            ->andReturn($categoryIds);

        $this->mockHomeRepository
            ->shouldReceive('getTopHeadings')
            ->with($categoryIds)
            ->once()
            ->andReturn($articles);

        $result = $this->homeService->getTopHeadings($userId);

        $this->assertInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class, $result);
    }

    public function test_get_preferred_articles(): void
    {
        $userId = 1;
        $sourceArticles = new \Illuminate\Pagination\LengthAwarePaginator(
            [new \Modules\Article\Entities\Article()], 1, 10, 1, []
        );
        $authorArticles = new \Illuminate\Pagination\LengthAwarePaginator(
            [new \Modules\Article\Entities\Article()], 1, 10, 1, []
        );
        $categoryArticles = new \Illuminate\Pagination\LengthAwarePaginator(
            [new \Modules\Article\Entities\Article()], 1, 10, 1, []
        );

        $expectedResult = [
            'sources' => new \Modules\Article\Transformers\ArticlesCollection($sourceArticles),
            'authors' => new \Modules\Article\Transformers\ArticlesCollection($authorArticles),
            'categories' => new \Modules\Article\Transformers\ArticlesCollection($categoryArticles),
        ];

        Cache::shouldReceive('remember')
            ->once()
            ->with(Mockery::type('string'), Mockery::any(), Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->mockHomeRepository
            ->shouldReceive('getPreferredSourceArticles')
            ->with($userId)
            ->once()
            ->andReturn($sourceArticles);

        $this->mockHomeRepository
            ->shouldReceive('getPreferredAuthorArticles')
            ->with($userId)
            ->once()
            ->andReturn($authorArticles);

        $this->mockHomeRepository
            ->shouldReceive('getPreferredCategoryArticles')
            ->with($userId)
            ->once()
            ->andReturn($categoryArticles);

        $result = $this->homeService->getPreferredArticles($userId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('sources', $result);
        $this->assertArrayHasKey('authors', $result);
        $this->assertArrayHasKey('categories', $result);

        $this->assertInstanceOf(\Modules\Article\Transformers\ArticlesCollection::class, $result['sources']);
        $this->assertInstanceOf(\Modules\Article\Transformers\ArticlesCollection::class, $result['authors']);
        $this->assertInstanceOf(\Modules\Article\Transformers\ArticlesCollection::class, $result['categories']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}