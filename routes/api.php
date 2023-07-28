<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompnayController;
use App\Models\User;
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
Route::get('cakes', [CompnayController::class, 'index']);
Route::group(['prefix' => 'account'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware("auth:sanctum");
    Route::get('unauthenticated', [AuthController::class, 'unauthenticated'])->name("auth.unauthenticated");
});

Route::group(['middleware' => ['auth:sanctum'],'prefix' => 'companies'], function () {
    Route::get('', [CompnayController::class, 'index']);
});

