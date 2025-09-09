<?php

use Illuminate\Support\Facades\Route;
use Modules\Provider\Http\Controllers\Api\ProviderController;

Route::get("providers", [ProviderController::class, "index"]);
Route::get("providers/{id}", [ProviderController::class, "show"]);
Route::post("providers/register", [ProviderController::class, "register"]);
Route::get("providers/sync", [ProviderController::class, "sync"]);
