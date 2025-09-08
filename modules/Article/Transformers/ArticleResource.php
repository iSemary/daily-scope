<?php

namespace modules\Article\Transformers;

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
            'source' => new \modules\Source\Transformers\SourceResource($this->whenLoaded('source')),
            'author' => new \modules\Author\Transformers\AuthorResource($this->whenLoaded('author')),
            'category' => new \modules\Category\Transformers\CategoryResource($this->whenLoaded('category')),
            'country' => new \modules\Country\Transformers\CountryResource($this->whenLoaded('country')),
            'language' => new \modules\Language\Transformers\LanguageResource($this->whenLoaded('language')),
        ];
    }
}
