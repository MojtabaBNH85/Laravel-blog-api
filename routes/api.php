<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);
Route::post("/logout", [AuthController::class, "logout"])->middleware('auth:sanctum');

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);
Route::get('/posts/{post}/comments', [CommentController::class, 'index']);

Route::get('/posts/{post}/reactions', [ReactionController::class, 'summary']);

Route::middleware('auth:sanctum')->group(function () {
    // Posts
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // Comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    //reaction to post
    Route::post('/posts/{post}/react', [ReactionController::class, 'react']);

    //user profile
    Route::get('/profile' , [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::delete('/profile', [UserController::class, 'destroy']);
    Route::delete('/profile/avatar', [UserController::class, 'destroyAvatar']);
});
