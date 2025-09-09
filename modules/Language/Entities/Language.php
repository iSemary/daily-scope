<?php

namespace Modules\Language\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function sources()
    {
        return $this->hasMany(\Modules\Source\Entities\Source::class);
    }

    public function articles()
    {
        return $this->hasMany(\Modules\Article\Entities\Article::class);
    }
}
