<?php

use App\Http\Controllers\Api\AuthController;
//use Illuminate\Http\Request;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);
Route::post("/logout", [AuthController::class, "logout"])->middleware('auth:sanctum');

Route::apiResource("/posts", PostController::class)->middleware('auth:sanctum');
Route::apiResource("/comments", CommentController::class)->except('show')->middleware('auth:sanctum');
