<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CampController;
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
Route::group(['prefix' => 'account'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('unauthenticated', [AuthController::class, 'unauthenticated'])->name("auth.unauthenticated");
    Route::get('unauthenticated', [AuthController::class, 'unauthenticated']);
});

Route::group(['middleware' => ['auth:sanctum'],'prefix' => 'batches'], function () {
    Route::get('', [BatchController::class, 'index']);
    Route::get('/{campId}/{day}', [BatchController::class, 'show']);
});

Route::group(['middleware' => ['auth:sanctum'],'prefix' => 'camps'], function () {
    Route::get('', [CampController::class, 'index']);
});

