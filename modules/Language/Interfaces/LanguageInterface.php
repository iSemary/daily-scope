<?php

namespace modules\Language\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use modules\Language\Entities\Language;

interface LanguageInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Language;
    public function findByCode(string $code): ?Language;
}
