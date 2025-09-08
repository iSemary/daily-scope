<?php

use Illuminate\Support\Facades\Route;
use modules\Language\Http\Controllers\Api\LanguageController;

Route::get("languages", [LanguageController::class, "index"]);
Route::get("languages/{code}", [LanguageController::class, "show"]);
