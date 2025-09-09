<?php

use App\Http\Controllers\Api\HomeController;
use Illuminate\Support\Facades\Route;
use Modules\Article\Http\Controllers\Api\ArticleController;

/**
 * API Version 1.0
 * 
 * This file contains the main API routes for version 1.0
 * Module-specific routes are included explicitly for better performance and clarity
 */

// Home Routes
Route::get("top-headings", [HomeController::class, "topHeadings"]);

// User preferred news
Route::group(['middleware' => 'auth:api'], function () {
    Route::get("preferred/articles", [HomeController::class, "preferredArticles"]);
});

// Today's news
Route::get("today", [ArticleController::class, "todayArticles"]);

// Search
Route::get("search", [ArticleController::class, "find"]);

// Author Module Routes
Route::group([], base_path('modules/Author/Routes/api.php'));

// Source Module Routes  
Route::group([], base_path('modules/Source/Routes/api.php'));

// Category Module Routes
Route::group([], base_path('modules/Category/Routes/api.php'));

// Article Module Routes
Route::group([], base_path('modules/Article/Routes/api.php'));

// Country Module Routes
Route::group([], base_path('modules/Country/Routes/api.php'));

// Language Module Routes
Route::group([], base_path('modules/Language/Routes/api.php'));

// Provider Module Routes
Route::group([], base_path('modules/Provider/Routes/api.php'));

// User Module Routes
Route::group([], base_path('modules/User/Routes/api.php'));
