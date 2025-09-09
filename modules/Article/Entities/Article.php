<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Article\Transformers\ArticlesResource;
use Modules\User\Entities\UserInterest;
use Modules\User\Interfaces\UserInterestTypes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
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

    public function source()
    {
        return $this->belongsTo(\Modules\Source\Entities\Source::class);
    }

    public function author()
    {
        return $this->belongsTo(\Modules\Author\Entities\Author::class);
    }

    // Accessor methods for getting related data through source
    public function getCategoryAttribute()
    {
        return $this->source ? $this->source->category : null;
    }

    public function getCountryAttribute()
    {
        return $this->source ? $this->source->country : null;
    }

    public function getLanguageAttribute()
    {
        return $this->source ? $this->source->language : null;
    }

    public function getProviderAttribute()
    {
        return $this->source ? $this->source->provider : null;
    }

    public function scopeBySourceAndArticleSlug(Builder $query, string $sourceSlug, string $articleSlug): Builder
    {
        return $query->join('sources', 'sources.id', 'articles.source_id')
            ->where('sources.slug', $sourceSlug)
            ->where('articles.slug', $articleSlug);
    }

    public function scopeBySourceAndAuthorSlug(Builder $query, string $sourceSlug, string $authorSlug): Builder
    {
        return $query->join('sources', 'sources.id', 'articles.source_id')
            ->join('authors', 'authors.id', 'articles.author_id')
            ->where('sources.slug', $sourceSlug)
            ->where('authors.slug', $authorSlug);
    }

    public function scopeWithArticleRelations(Builder $query): Builder
    {
        return $query->select(['articles.id', 'articles.title', 'articles.slug', 'articles.description', 'reference_url', 'body', 'image', 'is_head', 'articles.source_id', 'author_id', 'published_at'])->with([
            'source' => function ($query) {
                $query->select('id', 'title', 'slug', 'description', 'provider_id', 'category_id', 'country_id', 'language_id')
                    ->with([
                        'category' => function ($q) {
                            $q->select('id', 'title', 'slug');
                        },
                        'country' => function ($q) {
                            $q->select('id', 'name', 'code');
                        },
                        'language' => function ($q) {
                            $q->select('id', 'name', 'code');
                        },
                        'provider' => function ($q) {
                            $q->select('id', 'name');
                        },
                    ]);
            },
            'author' => function ($query) {
                $query->select('id', 'name', 'slug');
            },
        ]);
    }

    public function scopeByRelatedItemSlug(Builder $query, string $itemSlug, string $itemTable, string $itemForeignKey): Builder
    {
        return $query->join($itemTable, $itemTable . '.id', 'articles.' . $itemForeignKey)
            ->where($itemTable . '.slug', $itemSlug);
    }

    /**
     * Get preferred source articles for a user.
     * 
     * @param int $userId The user ID
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function getPreferredSourceArticles(int $userId)
    {
        $sourceIds = UserInterest::getItemIds($userId, UserInterestTypes::SOURCE);
        $sourceArticles = Article::with(['language', 'country', 'source', 'author', 'category', 'provider'])
            ->whereIn("articles.source_id", $sourceIds)
            ->orderByDesc("published_at")
            ->limit(8)
            ->get();

        return ArticlesResource::collection($sourceArticles);
    }

    /**
     * Get preferred category articles for a user.
     * 
     * @param int $userId The user ID
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function getPreferredCategoryArticles(int $userId)
    {
        $categoryIds = UserInterest::getItemIds($userId, UserInterestTypes::CATEGORY);
        $categoryArticles = Article::withArticleRelations()
            ->whereHas('source', function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->orderByDesc("published_at")
            ->limit(9)
            ->get();

        return ArticlesResource::collection($categoryArticles);
    }

    /**
     * Get preferred author articles for a user.
     * 
     * @param int $userId The user ID
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function getPreferredAuthorArticles(int $userId)
    {
        $authorIds = UserInterest::getItemIds($userId, UserInterestTypes::AUTHOR);
        $authorArticles = Article::withArticleRelations()
            ->whereIn("articles.author_id", $authorIds)
            ->orderByDesc("published_at")
            ->limit(9)
            ->get();

        return ArticlesResource::collection($authorArticles);
    }
}
