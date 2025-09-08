<?php

namespace modules\Article\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'description', 'reference_url', 'body', 'image', 'is_head', 'provider_id', 'source_id', 'category_id', 'author_id', 'language_id', 'published_at'];

    public function source() {
        return $this->belongsTo(\modules\Source\Entities\Source::class);
    }

    public function author() {
        return $this->belongsTo(\modules\Author\Entities\Author::class);
    }

    public function category() {
        return $this->belongsTo(\modules\Category\Entities\Category::class);
    }

    public function country() {
        return $this->belongsTo(\modules\Country\Entities\Country::class);
    }

    public function language() {
        return $this->belongsTo(\modules\Language\Entities\Language::class);
    }

    public function provider() {
        return $this->belongsTo(\modules\Provider\Entities\Provider::class);
    }

    public function scopeBySourceAndArticleSlug(Builder $query, string $sourceSlug, string $articleSlug): Builder {
        return $query->join('sources', 'sources.id', 'articles.source_id')
            ->where('sources.slug', $sourceSlug)
            ->where('articles.slug', $articleSlug);
    }

    public function scopeBySourceAndAuthorSlug(Builder $query, string $sourceSlug, string $authorSlug): Builder {
        return $query->join('sources', 'sources.id', 'articles.source_id')
            ->join('authors', 'authors.source_id', 'sources.id')
            ->where('sources.slug', $sourceSlug)
            ->where('authors.slug', $authorSlug);
    }

    public function scopeWithArticleRelations(Builder $query): Builder {
        return $query->select(['articles.id', 'articles.title', 'articles.slug', 'articles.description', 'reference_url', 'body', 'image', 'is_head', 'articles.provider_id', 'articles.source_id', 'articles.category_id', 'author_id', 'articles.language_id', 'published_at'])->with([
            'source' => function ($query) {
                $query->select('id', 'title', 'slug', 'description');
            },
            'author' => function ($query) {
                $query->select('id', 'name', 'slug');
            },
            'category' => function ($query) {
                $query->select('id', 'title', 'slug');
            },
            'country' => function ($query) {
                $query->select('id', 'name', 'code');
            },
            'language' => function ($query) {
                $query->select('id', 'name', 'code');
            },
            'provider' => function ($query) {
                $query->select('id', 'name');
            },
        ]);
    }

    public function scopeByRelatedItemSlug(Builder $query, string $itemSlug, string $itemTable, string $itemForeignKey): Builder {
        return $query->join($itemTable, $itemTable . '.id', 'articles.' . $itemForeignKey)
            ->where($itemTable . '.slug', $itemSlug);
    }
}
