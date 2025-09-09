<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'parent_id', 'order_number', 'status'];

    // Note: articles() relationship would be added when Article module is implemented
    // public function articles() {
    //     return $this->hasMany(Article::class);
    // }
}
