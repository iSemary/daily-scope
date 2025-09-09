<?php

namespace Tests\Unit;

use Tests\TestCase;
use Modules\Source\Entities\Source;
use Modules\Provider\Entities\Provider;
use Modules\Category\Entities\Category;
use Modules\Country\Entities\Country;
use Modules\Language\Entities\Language;
use Modules\Author\Entities\Author;
use Modules\Article\Entities\Article;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class SourceModelTest extends TestCase
{
    public function test_source_fillable_attributes(): void
    {
        $source = new Source();
        $expectedFillable = [
            'title',
            'slug',
            'description',
            'url',
            'provider_id',
            'category_id',
            'country_id',
            'language_id'
        ];

        $this->assertEquals($expectedFillable, $source->getFillable());
    }

    public function test_source_soft_deletes(): void
    {
        $source = new Source();
        $this->assertTrue(method_exists($source, 'trashed'));
    }

    public function test_source_has_factory(): void
    {
        $source = new Source();
        $this->assertTrue(method_exists($source, 'factory'));
    }

    public function test_source_provider_relationship(): void
    {
        $source = new Source();
        $provider = $source->provider();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $provider);
        $this->assertEquals('provider_id', $provider->getForeignKeyName());
        $this->assertEquals('id', $provider->getOwnerKeyName());
    }

    public function test_source_category_relationship(): void
    {
        $source = new Source();
        $category = $source->category();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $category);
        $this->assertEquals('category_id', $category->getForeignKeyName());
        $this->assertEquals('id', $category->getOwnerKeyName());
    }

    public function test_source_country_relationship(): void
    {
        $source = new Source();
        $country = $source->country();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $country);
        $this->assertEquals('country_id', $country->getForeignKeyName());
        $this->assertEquals('id', $country->getOwnerKeyName());
    }

    public function test_source_language_relationship(): void
    {
        $source = new Source();
        $language = $source->language();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $language);
        $this->assertEquals('language_id', $language->getForeignKeyName());
        $this->assertEquals('id', $language->getOwnerKeyName());
    }

    public function test_source_authors_relationship(): void
    {
        $source = new Source();
        $authors = $source->authors();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $authors);
        $this->assertEquals('source_id', $authors->getForeignKeyName());
        $this->assertEquals('id', $authors->getLocalKeyName());
    }

    public function test_source_articles_relationship(): void
    {
        $source = new Source();
        $articles = $source->articles();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $articles);
        $this->assertEquals('source_id', $articles->getForeignKeyName());
        $this->assertEquals('id', $articles->getLocalKeyName());
    }

    public function test_source_can_be_created(): void
    {
        $sourceData = [
            'title' => 'Test Source',
            'slug' => 'test-source',
            'description' => 'Test source description',
            'url' => 'https://example.com',
            'provider_id' => 1,
            'category_id' => 1,
            'country_id' => 1,
            'language_id' => 1,
        ];

        $source = new Source($sourceData);

        $this->assertEquals('Test Source', $source->title);
        $this->assertEquals('test-source', $source->slug);
        $this->assertEquals('Test source description', $source->description);
        $this->assertEquals('https://example.com', $source->url);
        $this->assertEquals(1, $source->provider_id);
        $this->assertEquals(1, $source->category_id);
        $this->assertEquals(1, $source->country_id);
        $this->assertEquals(1, $source->language_id);
    }

    public function test_source_can_be_created_with_nullable_fields(): void
    {
        $sourceData = [
            'title' => 'Test Source',
            'slug' => 'test-source',
            'description' => 'Test source description',
            'url' => 'https://example.com',
            'provider_id' => 1,
            'category_id' => null,
            'country_id' => null,
            'language_id' => null,
        ];

        $source = new Source($sourceData);

        $this->assertEquals('Test Source', $source->title);
        $this->assertEquals('test-source', $source->slug);
        $this->assertEquals('Test source description', $source->description);
        $this->assertEquals('https://example.com', $source->url);
        $this->assertEquals(1, $source->provider_id);
        $this->assertNull($source->category_id);
        $this->assertNull($source->country_id);
        $this->assertNull($source->language_id);
    }

    public function test_source_relationships_return_correct_models(): void
    {
        $source = new Source();

        // Test that relationships exist and return proper relationship objects
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $source->provider());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $source->category());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $source->country());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $source->language());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $source->authors());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $source->articles());
    }

    public function test_source_is_eloquent_model(): void
    {
        $source = new Source();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $source);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
