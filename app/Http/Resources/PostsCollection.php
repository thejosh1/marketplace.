<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => PostResource::collection($this->collection),
            'paginate' => [
                'count' => $this->count(),
                'total' => $this->total(),
                'per_page' => $this->perpage(),
                'current_page' => $this->currentpage(),
                'last_page' => $this->lastpage(),
            ]
        ];
    }
}
