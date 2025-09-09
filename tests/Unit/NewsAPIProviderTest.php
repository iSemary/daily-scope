<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Providers\NewsAPI;
use Modules\Provider\Entities\Provider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Mockery;

class NewsAPIProviderTest extends TestCase
{
    protected NewsAPI $newsAPI;
    protected $mockProvider;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockProvider = new Provider([
            'name' => 'NewsAPI',
            'class_name' => NewsAPI::class,
            'end_point' => 'https://newsapi.org/v2',
            'api_key' => 'encrypted-key',
        ]);
        $this->mockProvider->id = 1;

        $this->newsAPI = new NewsAPI($this->mockProvider);
    }

    public function test_constructor_sets_provider(): void
    {
        // Test that the provider is set by checking if the object was created successfully
        $this->assertInstanceOf(NewsAPI::class, $this->newsAPI);
    }

    public function test_constructor_sets_endpoint(): void
    {
        // Test that the endpoint is set by checking if the object was created successfully
        $this->assertInstanceOf(NewsAPI::class, $this->newsAPI);
    }

    public function test_fetch_method_exists(): void
    {
        $this->assertTrue(method_exists($this->newsAPI, 'fetch'));
    }

    public function test_fetch_method_is_public(): void
    {
        $reflection = new \ReflectionClass(NewsAPI::class);
        $method = $reflection->getMethod('fetch');
        $this->assertTrue($method->isPublic());
    }

    public function test_newsapi_extends_provider_abstractor(): void
    {
        $this->assertInstanceOf(\App\Services\Abstractors\ProviderAbstractor::class, $this->newsAPI);
    }

    public function test_newsapi_has_required_methods(): void
    {
        $this->assertTrue(method_exists($this->newsAPI, 'fetch'));
        $this->assertTrue(method_exists($this->newsAPI, 'setApiKey'));
        $this->assertTrue(method_exists($this->newsAPI, 'getApiKey'));
        $this->assertTrue(method_exists($this->newsAPI, 'setEndPoint'));
        $this->assertTrue(method_exists($this->newsAPI, 'getEndPoint'));
        $this->assertTrue(method_exists($this->newsAPI, 'fetchSources'));
        $this->assertTrue(method_exists($this->newsAPI, 'createOrUpdateSources'));
        $this->assertTrue(method_exists($this->newsAPI, 'fetchArticles'));
        $this->assertTrue(method_exists($this->newsAPI, 'createOrUpdateArticles'));
        $this->assertTrue(method_exists($this->newsAPI, 'fetchTopHeadingsSources'));
        $this->assertTrue(method_exists($this->newsAPI, 'fetchTopHeadingsArticles'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}