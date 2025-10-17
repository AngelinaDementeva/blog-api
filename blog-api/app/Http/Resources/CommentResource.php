<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'body'             => $this->body,
            'user'             => new UserResource($this->whenLoaded('user')),
            'commentable_type' => class_basename($this->commentable_type),
            'commentable_id'   => $this->commentable_id,
            'replies_count'    => $this->replies()->count(),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
