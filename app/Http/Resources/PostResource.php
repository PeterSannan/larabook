<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
                'body' => $this->body
            ],
            'links' => [
                'self' => url('/api/posts/' . $this->id)
            ]
        ];
    }
}
