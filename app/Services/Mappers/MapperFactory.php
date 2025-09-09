<?php

namespace App\Services\Mappers;

use App\Services\Mappers\Providers\NewsDataIOMapper;
use App\Services\Mappers\Providers\NewsAPIMapper;
use App\Services\Mappers\Providers\NewsAPIAiMapper;

class MapperFactory
{
    public static function create(string $providerType): object
    {
        return match ($providerType) {
            'NewsDataIO' => new NewsDataIOMapper(),
            'NewsAPI' => new NewsAPIMapper(),
            'NewsAPIAi' => new NewsAPIAiMapper(),
            default => throw new \InvalidArgumentException("Unsupported provider type: {$providerType}")
        };
    }
}
