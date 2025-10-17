<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     schema="CommentResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="body", type="string", example="Пример поста"),
 *     @OA\Property(property="user", type="object",
 *         @OA\Property(property="id", type="integer", example=5),
 *         @OA\Property(property="name", type="string", example="Angelina")
 *     ),
 *     @OA\Property(property="created_at", type="string", example="2025-10-16T12:34:56Z")
 * )
 */
class CommentResource {}
