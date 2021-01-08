<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;

class PostResource extends JsonResource
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
            'type' => 'posts',
            'id' => $this->id,
            'attributes' => [
                'posted_by' => new UserResource($this->user),
                'image' => Config::get('aws.s3_url')  . $this->image,
                'likes_count' => $this->likes->count(),
                'auth_user_liked' => $this->likes()->where('user_id', auth()->id())->exists(),
                'posted_at' => $this->created_at->diffForHumans(),
                'body' => $this->body,
                'comments' => CommentResource::collection($this->comments),
                'comments_count' => $this->comments->count()
            ],
            'links' => [
                'self' => url('/api/posts/' . $this->id)
            ]
        ];
    }
}
