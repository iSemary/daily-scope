<?php

namespace Modules\Source\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Source extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'description', 'url', 'provider_id', 'category_id', 'country_id', 'language_id'];

    public function provider()
    {
        return $this->belongsTo(\Modules\Provider\Entities\Provider::class);
    }

    public function category()
    {
        return $this->belongsTo(\Modules\Category\Entities\Category::class);
    }

    public function country()
    {
        return $this->belongsTo(\Modules\Country\Entities\Country::class);
    }

    public function language()
    {
        return $this->belongsTo(\Modules\Language\Entities\Language::class);
    }

    public function authors()
    {
        return $this->hasMany(\Modules\Author\Entities\Author::class);
    }

    public function articles()
    {
        return $this->hasMany(\Modules\Article\Entities\Article::class);
    }
}
