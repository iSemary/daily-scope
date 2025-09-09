<?php

namespace App\Services\Mappers;

use Illuminate\Support\Str;

abstract class BaseMapper implements MapperInterface
{
    protected function sanitizeString(?string $value, int $maxLength = null): string
    {
        if (empty($value)) {
            return '';
        }
        
        $value = trim($value);
        
        if ($maxLength && strlen($value) > $maxLength) {
            $value = substr($value, 0, $maxLength);
        }
        
        return mb_convert_encoding($value, "UTF-8");
    }

    protected function generateSlug(string $value, int $maxLength = 100): string
    {
        return Str::slug(substr($value, 0, $maxLength));
    }

    protected function parseDateTime(?string $dateTime): ?int
    {
        if (empty($dateTime)) {
            return null;
        }
        
        $timestamp = strtotime($dateTime);
        return $timestamp ? $timestamp : null;
    }

    protected function getCountryCode(string $countryName): string
    {
        $words = explode(' ', $countryName);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials;
    }
}
