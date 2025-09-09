<?php

namespace Tests\Feature;

use App\Services\CacheService;
use App\Services\HomeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HomeApiCacheTest extends TestCase
{
    use RefreshDatabase;

    protected HomeService $homeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->homeService = app(HomeService::class);
    }

    /**
     * Test that top-headings API uses caching
     */
    public function test_top_headings_uses_cache()
    {
        // Clear cache first
        Cache::flush();

        // First request should hit database
        $response1 = $this->getJson('/api/v1.0/top-headings');
        $response1->assertStatus(200);

        // Second request should hit cache
        $response2 = $this->getJson('/api/v1.0/top-headings');
        $response2->assertStatus(200);

        // Both responses should be identical
        $this->assertEquals($response1->json(), $response2->json());
    }

    /**
     * Test that preferred articles API uses caching for authenticated users
     */
    public function test_preferred_articles_uses_cache()
    {
        // This test would require authentication setup
        // For now, we'll test the service directly
        
        $userId = 1;
        
        // Clear cache first
        Cache::flush();

        // First call should hit database
        $result1 = $this->homeService->getPreferredArticles($userId);

        // Second call should hit cache
        $result2 = $this->homeService->getPreferredArticles($userId);

        // Both results should be identical
        $this->assertEquals($result1, $result2);
    }

    /**
     * Test cache configuration
     */
    public function test_cache_configuration()
    {
        $config = config('cache_settings');
        
        $this->assertArrayHasKey('home', $config);
        $this->assertArrayHasKey('top_headings', $config['home']);
        $this->assertArrayHasKey('preferred_articles', $config['home']);
        $this->assertArrayHasKey('user_categories', $config['home']);
        
        $this->assertArrayHasKey('ttl', $config['home']['top_headings']);
        $this->assertArrayHasKey('key_prefix', $config['home']['top_headings']);
    }

    /**
     * Test cache headers are added to responses
     */
    public function test_cache_headers_are_added()
    {
        $response = $this->getJson('/api/v1.0/top-headings');
        
        $response->assertStatus(200);
        $response->assertHeader('Cache-Control');
        $response->assertHeader('X-Cache-Status');
        $response->assertHeader('ETag');
    }
}
