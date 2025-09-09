<?php

use Illuminate\Support\Facades\Route;
use Modules\Country\Http\Controllers\Api\CountryController;

Route::get("countries", [CountryController::class, "index"]);
Route::get("countries/{code}", [CountryController::class, "show"]);
