<?php

namespace modules\Country\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use modules\Country\Entities\Country;

interface CountryInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Country;
    public function findByCode(string $code): ?Country;
}
