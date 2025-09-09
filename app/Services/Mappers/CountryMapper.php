<?php

namespace App\Services\Mappers;

class CountryMapper extends BaseMapper
{
    public function map(array $data): array
    {
        $country = $data['country'] ?? 'Unknown';
        
        if (is_array($country)) {
            $country = $country[0] ?? 'Unknown';
        }

        return [
            'name' => strtoupper($this->sanitizeString($country)),
            'code' => $this->getCountryCode($country),
        ];
    }
}
