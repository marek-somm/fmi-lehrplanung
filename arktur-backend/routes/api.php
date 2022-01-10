<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UpdateController;

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

Route::group(['middleware' => ['web']], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/check', [AuthController::class, 'check']);
    
    Route::middleware('auth:sanctum')->group(function() {
        Route::get('/search/event', [SearchController::class, 'searchEvent']);
        Route::get('/get/event', [SearchController::class, 'getEvent']);
        Route::get('/search/module', [SearchController::class, 'searchModule']);
        Route::get('/get/module', [SearchController::class, 'getModule']);
        Route::get('/get/new', [SearchController::class, 'getNewEntries']);
        Route::get('/search/person', [SearchController::class, 'searchPerson']);

        Route::get('/user/events', [SearchController::class, 'getUserEvents']);

        Route::put('/add/event', [UpdateController::class, 'addEvent']);
        Route::put('/remove/event', [UpdateController::class, 'removeEvent']);
    });
});