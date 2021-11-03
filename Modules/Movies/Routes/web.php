<?php

use Illuminate\Support\Facades\Route;
use Modules\Movies\Http\Controllers\MoviesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('movies')->group(function() {
    Route::get('/', [MoviesController::class, 'index']);
});
