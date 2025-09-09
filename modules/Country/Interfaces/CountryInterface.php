<?php

namespace Modules\Country\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Modules\Country\Entities\Country;

interface CountryInterface
{
    public function all(): Collection;
    public function findById(int $id): ?Country;
    public function findByCode(string $code): ?Country;
}
