<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\{PostStoreRequest, PostUpdateRequest};
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Posts",
 *     description="Управление постами"
 * )
 */
class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index','show','userActive']);
    }

    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Получить список постов",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="Список постов получен успешно",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PostResource"))
     *     )
     * )
     */
    public function index()
    {
        $posts = Post::with('user')->latest()->paginate();
        return PostResource::collection($posts);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Создать пост",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status","body"},
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="body", type="string", example="Мой новый пост")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Пост успешно создан"),
     *     @OA\Response(response=422, description="Ошибка валидации")
     * )
     */
    public function store(PostStoreRequest $request)
    {
        $post = Post::create([
            'status' => $request->status,
            'body'   => $request->body,
            'user_id'=> auth()->id(),
        ]);

        return new PostResource($post->load('user'));
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Получить пост по ID",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID поста",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Пост успешно получен")
     * )
     */
    public function show(Post $post)
    {
        return new PostResource($post->load('user'));
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Обновить пост",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID поста"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="draft"),
     *             @OA\Property(property="body", type="string", example="Обновлённый текст поста")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Пост обновлён"),
     *     @OA\Response(response=403, description="Нет прав на редактирование"),
     *     @OA\Response(response=422, description="Ошибка валидации")
     * )
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        $this->authorize('update', $post);
        $post->update($request->validated());
        return new PostResource($post->load('user'));
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Удалить пост",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID поста"
     *     ),
     *     @OA\Response(response=204, description="Пост удалён"),
     *     @OA\Response(response=403, description="Нет прав на удаление")
     * )
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return response()->noContent();
    }

    /**
     * @OA\Get(
     *     path="/api/users/{userId}/posts/active",
     *     summary="Активные посты пользователя",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID пользователя"
     *     ),
     *     @OA\Response(response=200, description="Успешный ответ")
     * )
     */
    public function userActive(int $userId)
    {
        $posts = Post::with('user')->active()->where('user_id',$userId)->paginate();
        return PostResource::collection($posts);
    }

    /**
     * @OA\Get(
     *     path="/api/me/posts",
     *     summary="Посты текущего пользователя",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Успешный ответ")
     * )
     */
    public function my()
    {
        $posts = auth()->user()->posts()->with('user')->latest()->paginate();
        return PostResource::collection($posts);
    }
}
