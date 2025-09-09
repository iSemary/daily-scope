<?php

use Illuminate\Support\Facades\Route;
use Modules\Article\Http\Controllers\Api\ArticleController;

Route::get("articles/{sourceSlug}/{slug}", [ArticleController::class, "show"]);
Route::get("search", [ArticleController::class, "find"]);
Route::get("search/deeply", [ArticleController::class, "findDeeply"]);
Route::get("today", [ArticleController::class, "todayArticles"]);
