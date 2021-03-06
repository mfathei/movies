<?php

use Illuminate\Support\Facades\Route;
use Modules\Movies\Http\Actions\ListMoviesAction;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/movies', ListMoviesAction::class)->name('movies.list');
