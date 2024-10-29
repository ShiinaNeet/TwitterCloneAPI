<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::apiResource('post', PostController::class);
Route::apiResource('posts.comments', CommentController::class);


Route::get('posts/popular', [PostController::class, 'popular'])->name('posts.popular');


// Route::get('posts/{post}/comments/most-liked', [CommentController::class, 'mostLiked'])->name('posts.comments.mostLiked');
// Route::get('posts/{post}/comments/recent', [CommentController::class, 'recent'])->name('posts.comments.recent');


