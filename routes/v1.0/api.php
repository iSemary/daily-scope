<?php

use Illuminate\Support\Facades\Route;

/**
 * API Version 1.0
 * 
 * This file contains the main API routes for version 1.0
 * Module-specific routes are included explicitly for better performance and clarity
 */

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
