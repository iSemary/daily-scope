<?php

namespace Modules\Language\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Modules\Language\Entities\Language;

interface LanguageInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Language;
    public function findByCode(string $code): ?Language;
}
