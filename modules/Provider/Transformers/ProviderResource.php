<?php

namespace Modules\Provider\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'class_name' => $this->class_name,
            'end_point' => $this->end_point,
            'fetched_at' => $this->fetched_at,
        ];
    }
}
