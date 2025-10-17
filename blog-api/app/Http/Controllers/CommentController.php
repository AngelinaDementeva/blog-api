<?php

namespace App\Http\Controllers;

use App\Http\Requests\{CommentStoreRequest, CommentUpdateRequest};
use App\Http\Resources\CommentResource;
use App\Models\{Comment, Post};
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Comments",
 *     description="Управление комментариями"
 * )
 */
class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['indexByPost','replies']);
    }

    /**
     * @OA\Post(
     *     path="/api/comments",
     *     summary="Создать комментарий",
     *     tags={"Comments"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"body", "commentable_type", "commentable_id"},
     *             @OA\Property(property="body", type="string", example="Отличный пост!"),
     *             @OA\Property(property="commentable_type", type="string", example="App\\Models\\Post"),
     *             @OA\Property(property="commentable_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Комментарий создан"),
     *     @OA\Response(response=422, description="Ошибка валидации")
     * )
     */
    public function store(CommentStoreRequest $request)
    {
        $commentableClass = $request->commentable_type;
        $commentable = $commentableClass::findOrFail($request->commentable_id);

        $comment = $commentable->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        return new CommentResource($comment->load('user'));
    }

    /**
     * @OA\Put(
     *     path="/api/comments/{id}",
     *     summary="Обновить комментарий",
     *     tags={"Comments"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID комментария"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="body", type="string", example="Обновленный комментарий")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Комментарий обновлён"),
     *     @OA\Response(response=403, description="Нет прав на редактирование")
     * )
     */
    public function update(CommentUpdateRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        $comment->update($request->validated());
        return new CommentResource($comment->load('user'));
    }

    /**
     * @OA\Delete(
     *     path="/api/comments/{id}",
     *     summary="Удалить комментарий",
     *     tags={"Comments"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID комментария"
     *     ),
     *     @OA\Response(response=204, description="Комментарий удалён"),
     *     @OA\Response(response=403, description="Нет прав на удаление")
     * )
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->noContent();
    }

    /**
     * @OA\Get(
     *     path="/api/me/comments",
     *     summary="Комментарии текущего пользователя",
     *     tags={"Comments"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Успешный ответ")
     * )
     */
    public function my()
    {
        $comments = auth()->user()->comments()->with('user')->latest()->paginate();
        return CommentResource::collection($comments);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{userId}/comments/active-posts",
     *     summary="Комментарии пользователя к активным постам",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID пользователя"
     *     ),
     *     @OA\Response(response=200, description="Успешный ответ")
     * )
     */
    public function byUserToActivePosts(int $userId)
    {
        $comments = Comment::with(['user','commentable' => function($m){ $m->with('user'); }])
            ->where('user_id', $userId)
            ->whereHasMorph('commentable', [Post::class], function($q){
                $q->active();
            })
            ->latest()
            ->paginate();

        return CommentResource::collection($comments);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{postId}/comments",
     *     summary="Комментарии поста",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         description="ID поста"
     *     ),
     *     @OA\Response(response=200, description="Успешный ответ")
     * )
     */
    public function indexByPost(int $postId)
    {
        $comments = Comment::with('user')
            ->where('commentable_type', Post::class)
            ->where('commentable_id', $postId)
            ->latest()
            ->paginate();

        return CommentResource::collection($comments);
    }

    /**
     * @OA\Get(
     *     path="/api/comments/{commentId}/replies",
     *     summary="Ответы на комментарий",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="commentId",
     *         in="path",
     *         required=true,
     *         description="ID комментария"
     *     ),
     *     @OA\Response(response=200, description="Успешный ответ")
     * )
     */
    public function replies(int $commentId)
    {
        $replies = Comment::with('user')
            ->where('commentable_type', Comment::class)
            ->where('commentable_id', $commentId)
            ->latest()
            ->paginate();

        return CommentResource::collection($replies);
    }
}
