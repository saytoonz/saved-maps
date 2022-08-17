<?php

use App\Http\Controllers\LocalMapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//local map
Route::group(['prefix'=>'local-map', 'as'=>'local-maps.'], function ()
{
    
    Route::post('save', [LocalMapController::class, 'save'])->name('save');
    Route::post('save-places-id', [LocalMapController::class, 'savePlacesId'])->name('save-places-id');
    Route::get('search', [LocalMapController::class, 'search'])->name('search');
    Route::get('history/{user_id}', [LocalMapController::class, 'history'])->name('history');
});
