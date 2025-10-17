<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, PostController, CommentController};

// Публичные маршруты
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);
Route::get('/users/{userId}/posts/active', [PostController::class, 'userActive']); // активные посты юзера

Route::get('/posts/{postId}/comments', [CommentController::class, 'indexByPost']);
Route::get('/comments/{commentId}/replies', [CommentController::class, 'replies']);

// Приватные маршруты (требуют токен)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD постов
    Route::post('/posts', [PostController::class, 'store']);
    Route::patch('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    Route::get('/me/posts', [PostController::class, 'my']); // посты текущего пользователя

    // Комментарии
    Route::post('/comments', [CommentController::class, 'store']);
    Route::patch('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::get('/me/comments', [CommentController::class, 'my']); // комменты текущего пользователя

    // Все комментарии пользователя к активным постам
    Route::get('/users/{userId}/comments/active-posts', [CommentController::class, 'byUserToActivePosts']);
});
