<?php

namespace App\Services\Mappers;

class LanguageMapper extends BaseMapper
{
    public function map(array $data): array
    {
        $language = $data['language'] ?? $data['lang'] ?? 'en';
        
        if (is_array($language)) {
            $language = $language[0] ?? 'en';
        }

        return [
            'name' => strtoupper($this->sanitizeString($language)),
            'code' => strtolower($this->sanitizeString($language)),
        ];
    }
}
