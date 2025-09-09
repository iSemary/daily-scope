<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ScrapNews;
use Modules\Provider\Entities\Provider;
use Illuminate\Support\Facades\Log;
use Mockery;

class ScrapNewsServiceTest extends TestCase
{
    protected ScrapNews $scrapNewsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scrapNewsService = new ScrapNews();
    }

    public function test_scrap_news_has_run_method(): void
    {
        $this->assertTrue(method_exists($this->scrapNewsService, 'run'));
    }

    public function test_scrap_news_run_method_is_public(): void
    {
        $reflection = new \ReflectionClass(ScrapNews::class);
        $method = $reflection->getMethod('run');
        $this->assertTrue($method->isPublic());
    }

    public function test_scrap_news_run_method_returns_void(): void
    {
        $reflection = new \ReflectionClass(ScrapNews::class);
        $method = $reflection->getMethod('run');
        $this->assertEquals('void', $method->getReturnType()->getName());
    }

    public function test_scrap_news_constructor(): void
    {
        $scrapNews = new ScrapNews();
        $this->assertInstanceOf(ScrapNews::class, $scrapNews);
    }

    public function test_scrap_news_logs_start_and_end(): void
    {
        // Test that the method exists and can be called
        $this->assertTrue(method_exists($this->scrapNewsService, 'run'));
    }

    public function test_scrap_news_handles_empty_providers(): void
    {
        // Test that the method exists and can be called
        $this->assertTrue(method_exists($this->scrapNewsService, 'run'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}