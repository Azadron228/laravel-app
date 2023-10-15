<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'post';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        return [
            'slug' => $this->resource->slug,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'body' => $this->resource->body,
            'tags' => new TagsCollection($this->resource->tags ?? []),
            'createdAt' => $this->resource->created_at,
            'updatedAt' => $this->resource->updated_at,
            'favorited' => $this->when(
                $user !== null,
                fn () =>
                $this->favorited->contains($user)
            ),
            'favoritesCount' => $this->favorited->count(),
            'user' => new ProfileResource($this->resource->user),
        ];
    }
}
