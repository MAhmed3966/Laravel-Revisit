<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


    Route::get("/dsas", function (Request $request) {
        return "these files are working";
    });

    Route::get('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'login'])->name('login');


    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/posts', [PostController::class,'getPosts'])->name('get.posts');
        Route::get('/update-posts/{id}', [PostController::class,'updatePost'])->name('update.posts');
    });
