<?php

namespace App\Services\Mappers;

class CategoryMapper extends BaseMapper
{
    public function map(array $data): array
    {
        $category = $data['category'] ?? 'General';
        
        if (is_array($category)) {
            $category = $category[0] ?? 'General';
        }

        return [
            'title' => ucfirst(strtolower($this->sanitizeString($category))),
            'slug' => strtolower($this->sanitizeString($category)),
        ];
    }
}
