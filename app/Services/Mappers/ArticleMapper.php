<?php

namespace App\Services\Mappers;

class ArticleMapper extends BaseMapper
{
    public function map(array $data): array
    {
        return [
            'title' => $this->sanitizeString($data['title'] ?? ''),
            'slug' => $this->generateSlug($data['title'] ?? ''),
            'description' => $this->sanitizeString($data['description'] ?? '', 1000),
            'body' => $this->sanitizeString($data['body'] ?? $data['content'] ?? '-'),
            'reference_url' => $this->sanitizeString($data['reference_url'] ?? $data['url'] ?? $data['link'] ?? ''),
            'image' => $this->sanitizeString($data['image'] ?? $data['image_url'] ?? $data['urlToImage'] ?? ''),
            'published_at' => $this->parseDateTime($data['published_at'] ?? $data['publishedAt'] ?? $data['pubDate'] ?? $data['dateTime'] ?? null),
            'is_head' => $data['is_head'] ?? false,
        ];
    }
}
