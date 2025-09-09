<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'index']);
Route::get('/README.md', [WelcomeController::class, 'readme']);
