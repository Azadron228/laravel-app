<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'profile';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        return array_merge(parent::toArray($request), [
            'following' => $this->when(
                $user !== null,
                fn () => $user->isFollowing($this->resource)
            ),
        ]);
    }
}
