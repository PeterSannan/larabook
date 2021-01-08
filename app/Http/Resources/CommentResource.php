<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'comments',
            'id' => $this->id,
            'attributes' => [
                'commented_by' => new UserResource($this->user), 
                'post_id' => $this->post_id,
                'comment' => $this->comment,
                'comment_at' => $this->created_at->diffForHumans()
            ],
            'links' => [
                'self' => url('/api/posts/' . $this->post_id)
            ]
        ];
    }
}
