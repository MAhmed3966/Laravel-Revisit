<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/posts', [PostController::class, 'getPosts'])->name('get.posts');
    Route::get('/update-posts/{id}', [PostController::class, 'updatePost'])->name('update.posts');
    Route::resource('products', ProductController::class);
});
