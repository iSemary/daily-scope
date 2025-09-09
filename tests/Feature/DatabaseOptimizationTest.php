<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Article\Entities\Article;
use Modules\Source\Entities\Source;
use Modules\Category\Entities\Category;
use Modules\Author\Entities\Author;
use Modules\Provider\Entities\Provider;
use Modules\Country\Entities\Country;
use Modules\Language\Entities\Language;
use Tests\TestCase;

class DatabaseOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedTestData();
    }

    /**
     * Test that articles can access related data through source relationships
     */
    public function test_article_relationships_work_through_source()
    {
        $article = Article::with(['source.category', 'source.country', 'source.language', 'source.provider'])->first();
        
        // Test that we can access category through source
        $this->assertNotNull($article->category);
        $this->assertEquals($article->source->category_id, $article->category->id);
        
        // Test that we can access country through source
        $this->assertNotNull($article->country);
        $this->assertEquals($article->source->country_id, $article->country->id);
        
        // Test that we can access language through source
        $this->assertNotNull($article->language);
        $this->assertEquals($article->source->language_id, $article->language->id);
        
        // Test that we can access provider through source
        $this->assertNotNull($article->provider);
        $this->assertEquals($article->source->provider_id, $article->provider->id);
    }

    /**
     * Test that filtering by category works through source relationship
     */
    public function test_filtering_by_category_through_source()
    {
        $category = Category::first();
        
        $articles = Article::whereHas('source', function ($query) use ($category) {
            $query->where('category_id', $category->id);
        })->get();
        
        $this->assertGreaterThan(0, $articles->count());
        
        // Verify all articles belong to the correct category
        foreach ($articles as $article) {
            $this->assertEquals($category->id, $article->source->category_id);
        }
    }

    /**
     * Test that withArticleRelations scope works with new structure
     */
    public function test_with_article_relations_scope()
    {
        $articles = Article::withArticleRelations()->get();
        
        $this->assertGreaterThan(0, $articles->count());
        
        foreach ($articles as $article) {
            // Verify all relationships are loaded
            $this->assertNotNull($article->source);
            $this->assertNotNull($article->author);
            $this->assertNotNull($article->category);
            $this->assertNotNull($article->country);
            $this->assertNotNull($article->language);
            $this->assertNotNull($article->provider);
        }
    }

    /**
     * Test that articles table has correct columns after optimization
     */
    public function test_articles_table_structure()
    {
        $article = new Article();
        $fillable = $article->getFillable();
        
        // Verify redundant columns are removed
        $this->assertNotContains('provider_id', $fillable);
        $this->assertNotContains('category_id', $fillable);
        $this->assertNotContains('language_id', $fillable);
        $this->assertNotContains('country_id', $fillable);
        
        // Verify essential columns remain
        $this->assertContains('source_id', $fillable);
        $this->assertContains('author_id', $fillable);
        $this->assertContains('title', $fillable);
        $this->assertContains('slug', $fillable);
    }

    /**
     * Test performance improvement with optimized queries
     */
    public function test_query_performance_improvement()
    {
        $startTime = microtime(true);
        
        // Test optimized query
        $articles = Article::withArticleRelations()
            ->whereHas('source', function ($query) {
                $query->where('category_id', 1);
            })
            ->get();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // This should be reasonably fast (adjust threshold as needed)
        $this->assertLessThan(1.0, $executionTime, 'Query execution time should be under 1 second');
        $this->assertGreaterThan(0, $articles->count());
    }

    /**
     * Seed test data for the tests
     */
    private function seedTestData()
    {
        // Create provider
        $provider = Provider::create([
            'name' => 'Test Provider',
            'class_name' => 'TestProvider',
            'end_point' => 'https://test.com',
            'api_key' => 'test-key'
        ]);

        // Create category
        $category = Category::create([
            'title' => 'Test Category',
            'slug' => 'test-category',
            'status' => 1
        ]);

        // Create country
        $country = Country::create([
            'name' => 'Test Country',
            'code' => 'TC'
        ]);

        // Create language
        $language = Language::create([
            'name' => 'Test Language',
            'code' => 'TL'
        ]);

        // Create source
        $source = Source::create([
            'title' => 'Test Source',
            'slug' => 'test-source',
            'description' => 'Test Description',
            'url' => 'https://test-source.com',
            'provider_id' => $provider->id,
            'category_id' => $category->id,
            'country_id' => $country->id,
            'language_id' => $language->id
        ]);

        // Create author
        $author = Author::create([
            'name' => 'Test Author',
            'slug' => 'test-author',
            'source_id' => $source->id
        ]);

        // Create articles
        for ($i = 1; $i <= 5; $i++) {
            Article::create([
                'title' => "Test Article {$i}",
                'slug' => "test-article-{$i}",
                'description' => "Test Description {$i}",
                'body' => "Test Body {$i}",
                'source_id' => $source->id,
                'author_id' => $author->id,
                'published_at' => time(),
                'is_head' => $i === 1 ? 1 : 0
            ]);
        }
    }
}
