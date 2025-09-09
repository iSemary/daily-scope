<?php

namespace App\Services\Mappers;

class SourceMapper extends BaseMapper
{
    public function map(array $data): array
    {
        return [
            'title' => $this->sanitizeString($data['title'] ?? $data['name'] ?? ''),
            'slug' => $this->generateSlug($data['slug'] ?? $data['id'] ?? $data['name'] ?? ''),
            'url' => $this->sanitizeString($data['url'] ?? '/'),
            'description' => $this->sanitizeString($data['description'] ?? ''),
        ];
    }
}
