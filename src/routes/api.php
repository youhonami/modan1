<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Api\FirebaseAuthController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\CommentController;
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
//ログイン機能
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
//新規会員登録機能
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/firebase-register', [FirebaseAuthController::class, 'register']);
//シェア機能
Route::post('/tweets', [TweetController::class, 'store']);
Route::get('/tweets', [TweetController::class, 'index']);
//シェアメッセージの削除
Route::delete('/tweets/{id}', [TweetController::class, 'destroy']);
//いいね機能
Route::middleware('api')->group(function () {
    Route::post('/tweets/{id}/like', [TweetController::class, 'like']);
    Route::delete('/tweets/{id}/like', [TweetController::class, 'unlike']);
});
//コメント機能
Route::get('/tweets/{id}', [TweetController::class, 'show']);
Route::get('/tweets/{id}/comments', [CommentController::class, 'index']);
Route::post('/tweets/{id}/comments', [CommentController::class, 'store']);
