<?php

namespace App\Http\Resources\V1;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'file' => $this->file,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated,
            'created_at_for_human' => $this->created_at_for_human,
            'updated_at_for_human' => $this->updated_at_for_human,
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
