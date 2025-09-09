<?php

namespace Modules\Language\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
        ];
    }
}
