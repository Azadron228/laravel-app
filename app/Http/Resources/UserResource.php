<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'user';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'email' => $this->resource->email,
            'username' => $this->resource->username,
            'bio' => $this->resource->bio,
            'avatar' => $this->resource->avatar,
        ]);
    }
}
