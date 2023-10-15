<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TagsCollection extends ResourceCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'tags';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = TagsResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection<int, mixed>
     */
    public function toArray($request)
    {
        return $this->collection->pluck('name');
    }
}
