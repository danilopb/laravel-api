<?php

use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\PostController;
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

Route::post('v1/login', [LoginController::class, 'login'])
    ->name('login');

Route::group([
    'prefix' => 'v1',
    'middleware'=> ['auth:sanctum', 'check.limit.hits']
], function(){
    Route::apiResource('posts', PostController::class);
    Route::post('posts/bulk', [ PostController::class, 'bulkStore' ])
        ->name('posts.bulk');
    Route::delete('posts/force-destroy/{post}', [ PostController::class, 'forceDestroy' ])
        ->name('posts.force_destroy');
});

