<?php

use Illuminate\Support\Facades\Route;

// Include module API routes
Route::group([], base_path('modules/Author/Routes/api.php'));
Route::group([], base_path('modules/Source/Routes/api.php'));
Route::group([], base_path('modules/Category/Routes/api.php'));
