<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;

class ImageResource extends JsonResource
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
            'type' => 'images',
                'id' => $this->id,
                'attributes' => [
                    'path' => Config::get('aws.s3_url') . $this->path,
                    'width'=> $this->width,
                    'heigh'=> $this->heigh,
                    'location'=> $this->location,
                ],
                'links' => [
                    'self'=> url('api/images/')
                ]
                ];
    }
}
