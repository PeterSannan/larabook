<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FriendRequestResource extends JsonResource
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
            'type' => 'friend-request',
            'id' => $this->id,
            'attributes' => [
                'confirmed_at' => optional($this->confirmed_at)->diffForHumans()
            ],
            'links' => [
                'self' => '/api/users/' . $this->friend_id
            ]
        ];
    }
}
