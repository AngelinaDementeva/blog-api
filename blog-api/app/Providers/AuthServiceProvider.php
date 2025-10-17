<?php

namespace App\Providers;

use App\Models\{Post, Comment};
use App\Policies\{PostPolicy, CommentPolicy};
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Политики авторизации приложения.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    /**
     * Регистрация любых служб аутентификации / авторизации.
     */
    public function boot(): void
    {
        // Здесь можно добавить Gates, если нужно
    }
}
