<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoriesCollection extends ResourceCollection
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
            'data' => CategoryResource::collection($this->collection),
            'paginate' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total_page' => $this->lastPage(),
            ]
        ];
    }
}
