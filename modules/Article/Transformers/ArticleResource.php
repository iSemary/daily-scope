<?php

namespace Modules\Article\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'reference_url' => $this->reference_url,
            'body' => $this->body,
            'image' => $this->image,
            'published_at' => date('F j, Y g:i a', $this->published_at),
            'source' => new \Modules\Source\Transformers\SourceResource($this->whenLoaded('source')),
            'author' => new \Modules\Author\Transformers\AuthorResource($this->whenLoaded('author')),
            'category' => new \Modules\Category\Transformers\CategoryResource($this->whenLoaded('category')),
            'country' => new \Modules\Country\Transformers\CountryResource($this->whenLoaded('country')),
            'language' => new \Modules\Language\Transformers\LanguageResource($this->whenLoaded('language')),
            'provider' => new \Modules\Provider\Transformers\ProviderResource($this->whenLoaded('provider')),
        ];
    }
}
