<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Mockery;

class CacheServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_cache_service_has_static_methods(): void
    {
        $this->assertTrue(method_exists(CacheService::class, 'clearHomeCaches'));
        $this->assertTrue(method_exists(CacheService::class, 'clearUserCaches'));
        $this->assertTrue(method_exists(CacheService::class, 'clearArticleCaches'));
        $this->assertTrue(method_exists(CacheService::class, 'clearUserInterestCaches'));
        $this->assertTrue(method_exists(CacheService::class, 'getCacheStats'));
    }

    public function test_get_cache_stats_redis(): void
    {
        config(['cache.default' => 'redis']);

        $mockRedis = Mockery::mock();
        $mockRedis->shouldReceive('info')
            ->once()
            ->andReturn([
                'redis_version' => '6.0.0',
                'used_memory_human' => '1.2M',
                'connected_clients' => '5',
                'total_commands_processed' => '1000'
            ]);

        $mockStore = Mockery::mock(\Illuminate\Cache\RedisStore::class);
        Cache::shouldReceive('getStore')
            ->once()
            ->andReturn($mockStore);
        Cache::shouldReceive('getRedis')
            ->once()
            ->andReturn($mockRedis);

        $result = CacheService::getCacheStats();

        $this->assertIsArray($result);
        $this->assertEquals('redis', $result['driver']);
        $this->assertEquals('6.0.0', $result['redis_version']);
        $this->assertEquals('1.2M', $result['used_memory']);
        $this->assertEquals('5', $result['connected_clients']);
        $this->assertEquals('1000', $result['total_commands_processed']);
    }

    public function test_get_cache_stats_non_redis(): void
    {
        config(['cache.default' => 'array']);

        $result = CacheService::getCacheStats();

        $this->assertIsArray($result);
        $this->assertEquals('array', $result['driver']);
        $this->assertEquals('Cache driver does not support statistics', $result['status']);
    }

    public function test_get_cache_stats_error(): void
    {
        config(['cache.default' => 'redis']);

        $mockStore = Mockery::mock(\Illuminate\Cache\RedisStore::class);
        Cache::shouldReceive('getStore')
            ->once()
            ->andReturn($mockStore);
        Cache::shouldReceive('getRedis')
            ->once()
            ->andThrow(new \Exception('Redis connection failed'));

        $result = CacheService::getCacheStats();

        $this->assertIsArray($result);
        $this->assertEquals('redis', $result['driver']);
        $this->assertEquals('Redis connection failed', $result['error']);
    }

    public function test_cache_service_is_instantiable(): void
    {
        $cacheService = new CacheService();
        $this->assertInstanceOf(CacheService::class, $cacheService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}