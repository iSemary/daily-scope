<?php

namespace modules\Language\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model {
    use HasFactory;

    protected $fillable = ['name', 'code'];
}
