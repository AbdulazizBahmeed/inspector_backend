<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\camps\CampController;
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

Route::group(['middleware' => ['auth:sanctum'],'prefix' => 'home-page'], function () {
    Route::get('camps', [CampController::class, 'getAllCamps']);
    Route::get('batches', [CampController::class, 'getAllBatches']);
});

