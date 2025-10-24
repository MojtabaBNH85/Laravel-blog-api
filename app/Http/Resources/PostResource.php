<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image_url' => $this->image ? asset('storage/' , $this->image) : null,
            'author' => new UserResource($this->whenLoaded('user')),
            'likes' => $this->likes ?? 0,
            'dislikes' => $this->dislikes ?? 0,
            'created_at' => $this->created_at->diffForHumans(),

        ];
    }
}
