<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});





// Route::get('posts/{post}/comments/most-liked', [CommentController::class, 'mostLiked'])->name('posts.comments.mostLiked');
// Route::get('posts/{post}/comments/recent', [CommentController::class, 'recent'])->name('posts.comments.recent');


