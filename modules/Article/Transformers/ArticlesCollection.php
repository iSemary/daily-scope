<?php

namespace modules\Article\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticlesCollection extends ResourceCollection {
    public function toArray($request): array {
        $response = [
            'data' => $this->collection
        ];
        
        if ($this->resource instanceof \Illuminate\Pagination\AbstractPaginator) {
            $response['meta'] = [
                'total' => $this->resource->total(),
                'current_page' => $this->resource->currentPage(),
                'per_page' => $this->resource->perPage(),
                'last_page' => $this->resource->lastPage(),
            ];
        }

        return $response;
    }
}
