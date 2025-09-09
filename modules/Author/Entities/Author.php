<?php

namespace Modules\Author\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'source_id'];

    public function source()
    {
        return $this->belongsTo(\Modules\Source\Entities\Source::class);
    }

    public function articles()
    {
        return $this->hasMany(\Modules\Article\Entities\Article::class);
    }
}
