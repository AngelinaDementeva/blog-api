<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'status'        => $this->status,
            'body'          => $this->body,
            'user'          => new UserResource($this->whenLoaded('user')),
            'comments_count'=> $this->comments()->count(),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
