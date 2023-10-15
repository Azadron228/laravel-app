<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('user/login', [AuthController::class, 'login'])->name('login');
Route::post('user/register', [AuthController::class, 'register'])->name('register');
Route::put('user/', [UserController::class, 'update'])->name('register');

Route::get('posts', [PostController::class, 'index'])->name('register');
Route::get('user', [PostController::class, 'index'])->name('register');
Route::get('tags', [TagController::class, 'list']);

Route::apiResource('profiles', ProfileController::class);
// Route::apiResource('user/', UserController::class);
Route::post('posts', [PostController::class, 'store'])->name('register');



Route::prefix('posts')->group(function () {
    Route::get('{slug}/comments', [CommentController::class, 'index']); // Route to the index method
    // Route::apiResource('{slug}/comments', CommentController::class)
    // ->except('index'); // Exclude the index method from the resource routes
});



Route::middleware('auth:sanctum')->group(function () {
    Route::post('user/updateavatar', [UserController::class, 'updateAvatar']);
    Route::post('profiles/{username}/follow', [ProfileController::class, 'follow']);
    Route::delete('profiles/{username}/follow', [ProfileController::class, 'unfollow']);
    Route::put('user', [UserController::class, 'update'])->name('register');
    Route::apiResource('posts', PostController::class)->except('index');

    Route::post('posts/{slug}/favorite', [PostController::class, 'favorite']);
    Route::delete('posts/{slug}/favorite', [PostController::class, 'unfavorite']);


    Route::get('user', [UserController::class, 'index'])->name('register');
    Route::apiResource('posts.comments', CommentController::class)->except('index');
    Route::apiResource('profiles.follow', UserController::class);
});
