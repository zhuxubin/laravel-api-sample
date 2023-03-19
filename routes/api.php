<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageManipulationController;
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

// 用户注册和登录
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    // 用户退出登录
    Route::post('/logout', [AuthController::class, 'logout']);

    // 专辑相关
    Route::apiResource('album', AlbumController::class);

    // 图片片相关
    Route::get('image', [ImageManipulationController::class, 'index']);
    Route::get('image/{image}', [ImageManipulationController::class, 'show']);
    Route::get('image/by-album/{album}', [ImageManipulationController::class, 'getByAlbum']);
    Route::delete('image/{image}', [ImageManipulationController::class, 'destroy']);

    Route::post('image', [ImageManipulationController::class, 'store']);

});

