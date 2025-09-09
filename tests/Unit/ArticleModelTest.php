<?php

namespace Tests\Unit;

use Tests\TestCase;
use Modules\Article\Entities\Article;
use Modules\Source\Entities\Source;
use Modules\Author\Entities\Author;
use Modules\Category\Entities\Category;
use Modules\Country\Entities\Country;
use Modules\Language\Entities\Language;
use Modules\Provider\Entities\Provider;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class ArticleModelTest extends TestCase
{
    public function test_article_fillable_attributes(): void
    {
        $article = new Article();
        $expectedFillable = [
            'title',
            'slug',
            'description',
            'reference_url',
            'body',
            'image',
            'is_head',
            'source_id',
            'author_id',
            'published_at'
        ];

        $this->assertEquals($expectedFillable, $article->getFillable());
    }

    public function test_article_soft_deletes(): void
    {
        $article = new Article();
        $this->assertTrue(method_exists($article, 'trashed'));
    }

    public function test_article_has_factory(): void
    {
        $article = new Article();
        $this->assertTrue(method_exists($article, 'factory'));
    }

    public function test_article_source_relationship(): void
    {
        $article = new Article();
        $source = $article->source();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $source);
        $this->assertEquals('source_id', $source->getForeignKeyName());
        $this->assertEquals('id', $source->getOwnerKeyName());
    }

    public function test_article_author_relationship(): void
    {
        $article = new Article();
        $author = $article->author();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $author);
        $this->assertEquals('author_id', $author->getForeignKeyName());
        $this->assertEquals('id', $author->getOwnerKeyName());
    }

    public function test_article_category_accessor(): void
    {
        $article = new Article();
        $article->source_id = 1;

        $mockSource = Mockery::mock(Source::class);
        $mockCategory = Mockery::mock(Category::class);
        
        $mockSource->shouldReceive('getAttribute')
            ->with('category')
            ->andReturn($mockCategory);

        $article->setRelation('source', $mockSource);

        $result = $article->getCategoryAttribute();

        $this->assertInstanceOf(Category::class, $result);
    }

    public function test_article_country_accessor(): void
    {
        $article = new Article();
        $article->source_id = 1;

        $mockSource = Mockery::mock(Source::class);
        $mockCountry = Mockery::mock(Country::class);
        
        $mockSource->shouldReceive('getAttribute')
            ->with('country')
            ->andReturn($mockCountry);

        $article->setRelation('source', $mockSource);

        $result = $article->getCountryAttribute();

        $this->assertInstanceOf(Country::class, $result);
    }

    public function test_article_language_accessor(): void
    {
        $article = new Article();
        $article->source_id = 1;

        $mockSource = Mockery::mock(Source::class);
        $mockLanguage = Mockery::mock(Language::class);
        
        $mockSource->shouldReceive('getAttribute')
            ->with('language')
            ->andReturn($mockLanguage);

        $article->setRelation('source', $mockSource);

        $result = $article->getLanguageAttribute();

        $this->assertInstanceOf(Language::class, $result);
    }

    public function test_article_provider_accessor(): void
    {
        $article = new Article();
        $article->source_id = 1;

        $mockSource = Mockery::mock(Source::class);
        $mockProvider = Mockery::mock(Provider::class);
        
        $mockSource->shouldReceive('getAttribute')
            ->with('provider')
            ->andReturn($mockProvider);

        $article->setRelation('source', $mockSource);

        $result = $article->getProviderAttribute();

        $this->assertInstanceOf(Provider::class, $result);
    }

    public function test_article_accessor_without_source(): void
    {
        $article = new Article();
        $article->source_id = null;

        $this->assertNull($article->getCategoryAttribute());
        $this->assertNull($article->getCountryAttribute());
        $this->assertNull($article->getLanguageAttribute());
        $this->assertNull($article->getProviderAttribute());
    }

    public function test_article_scope_by_source_and_article_slug(): void
    {
        $query = Article::query();
        $result = $query->bySourceAndArticleSlug('test-source', 'test-article');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $result);
    }

    public function test_article_scope_by_source_and_author_slug(): void
    {
        $query = Article::query();
        $result = $query->bySourceAndAuthorSlug('test-source', 'test-author');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $result);
    }

    public function test_article_scope_with_article_relations(): void
    {
        $query = Article::query();
        $result = $query->withArticleRelations();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $result);
    }

    public function test_article_scope_by_related_item_slug(): void
    {
        $query = Article::query();
        $result = $query->byRelatedItemSlug('test-slug', 1, 'test-key');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $result);
    }

    public function test_article_can_be_created(): void
    {
        $articleData = [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'description' => 'Test description',
            'reference_url' => 'https://example.com',
            'body' => 'Test body content',
            'image' => 'test-image.jpg',
            'is_head' => true,
            'source_id' => 1,
            'author_id' => 1,
            'published_at' => now(),
        ];

        $article = new Article($articleData);

        $this->assertEquals('Test Article', $article->title);
        $this->assertEquals('test-article', $article->slug);
        $this->assertEquals('Test description', $article->description);
        $this->assertEquals('https://example.com', $article->reference_url);
        $this->assertEquals('Test body content', $article->body);
        $this->assertEquals('test-image.jpg', $article->image);
        $this->assertTrue($article->is_head);
        $this->assertEquals(1, $article->source_id);
        $this->assertEquals(1, $article->author_id);
        $this->assertInstanceOf(\Carbon\Carbon::class, $article->published_at);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
