<?php

namespace Modules\Author\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource {
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
