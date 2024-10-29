<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// No middleware. This route is public...
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);



    Route::apiResource('post', PostController::class);
    Route::apiResource('posts.comments', CommentController::class);


    Route::get('posts/popular', [PostController::class, 'popular'])->name('posts.popular');


    Route::post('posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');


    Route::put('posts/{post}/comments', [CommentController::class, 'update'])->name('posts.comments.update');

});
