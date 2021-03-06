<?php

namespace App\Http\Resources;

use App\Friend;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'type' => 'users',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'friendship' => new FriendRequestResource(Friend::friendship($this->id)),
                'cover_image' => new ImageResource($this->cover_image),
                'profile_image' => new ImageResource($this->profile_image)
            ],
            'links' => [
                'self' => url('/api/users/' . $this->id)
            ]
        ];
    }
}
