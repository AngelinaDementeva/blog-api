<?php

namespace App\Http\Requests;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $map = [
            'post' => Post::class,
            'comment' => Comment::class,
        ];

        if ($this->has('commentable_type') && isset($map[$this->commentable_type])) {
            $this->merge(['commentable_type' => $map[$this->commentable_type]]);
        }
    }

    public function rules(): array
    {
        return [
            'body' => ['required','string','min:1'],
            'commentable_type' => ['required','string','in:' . Post::class . ',' . Comment::class . ',post,comment'],
            'commentable_id'   => ['required','integer','min:1'],
        ];
    }
}
