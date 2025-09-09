<?php

namespace modules\Language\Services;

use Illuminate\Database\Eloquent\Collection;
use modules\Language\Entities\Language;
use modules\Language\Interfaces\LanguageInterface;

class LanguageService
{
    private LanguageInterface $languageRepository;

    public function __construct(LanguageInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function list(): Collection
    {
        return $this->languageRepository->all();
    }

    public function show(int $id): ?Language
    {
        return $this->languageRepository->findById($id);
    }

    public function showByCode(string $code): ?Language
    {
        return $this->languageRepository->findByCode($code);
    }
}
