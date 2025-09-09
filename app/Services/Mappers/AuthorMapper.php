<?php

namespace App\Services\Mappers;

class AuthorMapper extends BaseMapper
{
    public function map(array $data, string $defaultName = ''): array
    {
        $name = $this->sanitizeString($data['author'] ?? $data['name'] ?? $defaultName);
        
        if (empty($name)) {
            $name = $defaultName ?: 'Unknown Author';
        }

        return [
            'name' => $name,
            'slug' => $this->generateSlug($name),
        ];
    }
}
